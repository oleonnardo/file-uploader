## File Uploader

Biblioteca de manipulação de upload arquivos com HTML usando o PHP

**Instruções:**

Utilize a função  `uploader()`  para receber a instância da classe.

> Realizar o upload de um arquivo:
>  
    $files = uploader()
        ->parameter('filenamePost')
        ->path('images/');
> 
> Validação do(s) arquivo(s):
> 
    $files = uploader()
         ->parameter('filenamePost')
         ->path('images/')
         ->addValidations([  
             'type' => ['jpg', 'png'],  
             'filesize' => '2MB', 
             //'type' => 'pdf',  
         ]); 
         // options available for validation: "filesize" and "type"
             
	if ($files->validate()) {  
        $arquivo = $files->save(); //returns a string with the filename
	} else {  
        print_r($files->errors()); //returns an array with the errors 
	}

> Ativar a opção de múltiplos arquivos:

    $file = uploader()  
        [...]
        ->multipleFiles() //or multipleFiles(true)
        ->save();
        
> Alterando um arquivo:

    $files = uploader() 
         ->parameter('file')
         ->path('files/')
         ->update($oldFilename);
> Deletando um arquivo

    uploader()
	    ->get($filename)  
	    ->delete();
> Funções disponíveis para manipular arquivos:
> 
    $arquivo = uploader()->get($directory . $filename);
    
    print_r($arquivo->filename());  
    print_r($arquivo->path());  
    print_r($arquivo->extension());  
    print_r($arquivo->basename());  
    print_r($arquivo->dirname());
    print_r($arquivo->delete());
