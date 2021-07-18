## File Uploader

Biblioteca de manipulação de upload arquivos com HTML usando o PHP

**Instruções:**

Utilize a função  `uploader()`  para receber a instância da classe.

> Realizar o upload de um arquivo:
>  
    $files = uploader()
             ->parameter('filenamePost')
             ->path('images/');
> Ativar a opção de múltiplos arquivos:
> 
    $files = uploader()
             ->parameter('filenamePost')
             ->path('images/')
             ->addValidations([  
                 'type' => ['jpg', 'png'],  
                 'filesize' => '2MB', 
                 //'type' => 'pdf',  
             ]); 
             //the options available for validation: "filesize" and "type"
             
	if ($files->validate()) {  
	    $arquivo = $files->save(); //returns a string with the filename
	} else {  
	    print_r($files->errors());
	}



