<?PHP
class tvs_exception extends Exception
{
  var $variables;
  // Red�finissez l'exception ainsi le message n'est pas facultatif
  public function __construct($chemin, $variables = array()) {
	$this->variables=$variables;
    // assurez-vous que tout a �t� assign� proprement
    parent::__construct($chemin, 0);
  }

  // cha�ne personnalis� repr�sentant l'objet
  public function __toString() {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

  
  public function get_infos() {
    return (array("message"=>$this->message, "variables"=>$this->variables));
  }
  
  public function get_exception() {
        $e=$this->get_infos();
        return (get_exception($e));
  }
  
}









?>