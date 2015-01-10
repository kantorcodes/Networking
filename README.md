<h1> Easy Networking For PHP </h1>


use Drapor\Networking\Networking;

class FooService extends Networking{




  public function getBar($id, $active){
 
      $endpoint = "/users/{$id}";
      $type     = "get";
 
      $res = $this->send(['active' => $active],$endpoint,$type);  
      
      return $res;
 
  }



}
