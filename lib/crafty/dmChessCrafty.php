<?php

class dmChessCrafty extends dmConfigurable
{
  protected
  $serviceContainer;
  
  public function __construct(dmBaseServiceContainer $serviceContainer, array $options)
  {
    $this->serviceContainer = $serviceContainer;
    
    $this->initialize($options);
  }
  
  protected function initialize(array $options)
  {
    $this->configure($options);
    
    $this->setOption('result_file', dmOs::join(sfConfig::get('sf_cache_dir'), '/crafty_'.dmString::random()));
  }
  
  public function execute($forsythe)
  {
    return $this->runPlayCommand($forsythe);
  }
  
  protected function runPlayCommand($forsytheNotation)
  {
    $fs = $this->serviceContainer->get('filesystem');
    $file = $this->getOption('result_file');
    $fs->mkdir(dirname($file));
    touch($file);
    $fs->chmod($file, 0777);
    file_put_contents($file, '');
    
    $command = $this->getPlayCommand($forsytheNotation);
    
    ob_start();
    passthru($command, $code);
    $return = ob_get_clean();
    
    if($code !== 0)
    {
//      dmDebug::kill($command, $return, $code, file($file));
      throw new dmChessCraftyException(sprintf('Can not run crafty: '.$command.' '.$return));
    }
    
    $forsythe = $this->extractForsythe(file($file, FILE_IGNORE_NEW_LINES));
    
    if(!$forsythe)
    {
//      dmDebug::kill($command, $return, $code, file($file));
      throw new dmChessCraftyException(sprintf('Can not run crafty: '.$command.' '.$return));
    }
    
    $fs->remove($file);
    
    return $forsythe;
  }
  
  protected function extractForsythe($results)
  {
    return str_replace('setboard ', '', $results[0]);
  }
  
  protected function getPlayCommand($forsytheNotation)
  {
    return sprintf("cd %s && %s log=off ponder=off %s <<EOF
setboard %s
move
savepos %s
quit
EOF",
    dirname($this->getOption('result_file')),
    $this->getOption('path'),
    $this->getCraftyLevel(),
    $forsytheNotation,
    basename($this->getOption('result_file'))
    );
  }

  protected function getCraftyLevel()
  {
    /*
     * st is the time in seconds crafty can think about the situation
     */
    return "st=".(round($this->getOption('level')/10, 2));
  }
}
