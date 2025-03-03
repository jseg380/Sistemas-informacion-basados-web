<?php

$avatars = [""];

class Comment {
  private $avatar;
  private $date;
  private $username;
  private $text;
  private $email;

  public function __construct($username, $date, $text, $email, $avatar = "") {
    $this->username = $username;
    $this->date = $date;
    $this->text = $text;
    $this->email = $email;
    global $avatars;
    $this->avatar = ($avatar == "") ? $avatars[rand(0, count($avatars))] : $avatar;
  }

  public function getComment() {
    return array("username" => $this->username,
                 "date" => $this->date,
                 "text" => $this->text,
                 "avatar" => $this->avatar);
  }
}

?>
