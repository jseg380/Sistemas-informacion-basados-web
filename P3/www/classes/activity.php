<?php

class Activity {
  private $name;
  private $logo;
  private $date;
  private $price;
  private $text;
  private $materials;
  private $links;

  public function __construct($name, $logo, $date, $price, $text, $materials, $links) {
    $this->name = $name;
    $this->logo = $logo;
    $this->date = $date;
    $this->price = $price;
    $this->text = $text;
    $this->materials = $materials;
    $this->links = $links;
  }

  public function getActivity() {
    return [
      'name' => $this->name,
      'logo' => $this->logo,
      'date' => $this->date,
      'price' => $this->price,
      'text' => $this->text,
      'materials' => $this->materials,
      'links' => $this->links
    ];
  }
}

?>
