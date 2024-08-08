<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assets;


class ContactDTO {


   #[Assets\NotBlank]
   #[Assets\Length(min: 5 , max:50)]
   public String $name = " ";

   #[Assets\NotBlank]
   #[Assets\Email]
   public String $email = " ";


   #[Assets\NotBlank]
   #[Assets\Length(min: 5 , max:50)]
   public String $message = " ";

   #[Assets\NotBlank]
   public String $service = '' ;
   
}
