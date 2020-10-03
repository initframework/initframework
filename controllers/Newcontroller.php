<?php
namespace Controllers;
use Library\Http\Request;

class Newcontroller
{

   public function __invoke(Request $req)
   {
      if ($req->requestUri == '/') {
         echo <<<HTML
         <button onclick="ajaxReq()">Make Ajax req</button>
         <script>
         async function ajaxReq() {
            let resp = await fetch('/users',{
               body: null,
               headers: new Headers(),
               method: 'GET'
            });
            resp.json();
            console.log(resp);
         }
         </script>
HTML;
      } else {
         echo "Go to <a href='/'>Home</a>";
      }
   }

}
