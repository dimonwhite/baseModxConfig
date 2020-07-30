<?php

require_once dirname(__FILE__) . '/console.class.php';

class ConsoleSaveFileProcessor extends modConsoleProcessor{
    
    public function process() {
        $code = trim($this->getProperty('code',''));
        $fileName = trim($this->getProperty('name',''));
        
        if(!$fileName = str_replace('..', '', $fileName)){
            return $this->failure("Filename is empty");
        }
        
        $path = realpath($this->modx->getOption('console_core_path', NULL, $this->modx->getOption('core_path') . 'components/console/').'files') .'/';
        
        $pi = pathinfo($fileName);
        
        if($pi['dirname'] != '.'){
            $path .= $pi['dirname'] .'/';
        }
        
        if (!is_dir($path) && !mkdir($path, 0755, true)){
            return $this->failure($this->modx->lexicon('console_err_path_nf'));
        }
        
        $file = $path.$pi['basename'].'.php'; 
        if($code){
            if(!file_put_contents($file, $code )){
                return $this->failure('Cannot write file');
            }
        }
        else if(file_exists($file)){
            unset($file);
        }
        
        return $this->success('');
    }
}

return 'ConsoleSaveFileProcessor';