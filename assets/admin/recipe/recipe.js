import $ from "jquery";
import bootbox from "bootbox";


var Recipe = {
   deleteRecipe : function() {
      $('body').on('click',"#delete-recipe", function() {
         bootbox.confirm({
            title : "Confirmation",
            Message : "supprimer ce recette",
            buttons : {
               confirm : {
                  label : "supprimer",
                  className : "btn btn-danger",
               },
               cancel : {
                  label : "Retourner",
                  className : "btn btn-primary",
               }
            },
            callback : function(resp){
               console.log("suppression ");
               
            }
         })
      })
   }
}

export {Recipe};