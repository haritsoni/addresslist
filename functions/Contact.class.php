<?php
//incomplete functions
//last update date: 17/03/2014
// function getName working
class Contact {

    private $id;
    private $db;
    private $con;
    public $first_name;
    public $last_name;
    public $email;
    public $phone_number;

    function __construct($id = null) {
        require_once '../include/DB_Connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->con = $this->db->connect();
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getName($id) {
        if ($id == 1) {
            $name = array('first_name' => "harit soni");
        } else {
            try {
                $res = mysqli_query($this->con, "select * from `contacts` where `id`='1'") or mysqli_error($this->con);
                $contact = array();
                while ($row = mysqli_fetch_assoc($res)) {
                    $contact['first_name'] = $row['first_name'];
                }
                return $contact;

                //$a = var_dump($res);
                //file_put_contents("haritsoni.txt", var_export($res,true));
            } catch (Exception $e) {
                //file_put_contents("haritsoni.txt", $e->getMessage());
                return array("error" => $e->getMessage());
            }

        }
        //$name = array('first_name' => "anything");
        return $name;
    }


    public function update() {
        $query = "  UPDATE contacts
                    SET first_name='" . $this->first_name . "', last_name='" . $this->last_name . "', email='" . $this->email . "', phone_number='" . $this->phone_number . "'
                    WHERE id='" . $this->id . "'";

        if ($res = mysql_query($query))
            return true;
        else
            return false;
    }

    public function delete() {
        $query = "  DELETE FROM contacts
                    WHERE id='" . $this->id . "'";
        if ($res = mysql_query($query))
            return true;
        else
            return false;
    }

    public function insert() {
        $query = "  INSERT INTO contacts VALUES
                    (null,
                     '" . $this->first_name . "',
                     '" . $this->last_name . "',
                     '" . $this->email . "',
                     '" . $this->phone_number . "')";
        if ($res = mysql_query($query))
            return true;
        else
            return false;
    }

    function searchAddressBook($query) {
        $contacts = Contact::search($query);
        //return $contacts;
        $results = array();
        foreach ($contacts as $contact) {
            $tempArray = array('id' => $contact->getId(),
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
                'email' => $contact->email,
                'phone_number' => $contact->phone_number
            );
            array_push($results, $tempArray);
        }

        return $results;
    }

    function deleteContact($in_contact) {
        $contact = new Contact($in_contact['id']);
        return $contact->delete();
    }

    function insertContact($in_contact) {
        $contact = new Contact(0);
        $contact->first_name = mysql_real_escape_string($in_contact['first_name']);
        $contact->last_name = mysql_real_escape_string($in_contact['last_name']);
        $contact->email = mysql_real_escape_string($in_contact['email']);
        $contact->phone_number = mysql_real_escape_string($in_contact['phone_number']);
        if ($contact->insert())
            return true;
    }

    function updateContact($in_contact) {
        $contact = new Contact($in_contact['id']);
        $contact->first_name = mysql_real_escape_string($in_contact['first_name']);
        $contact->last_name = mysql_real_escape_string($in_contact['last_name']);
        $contact->email = mysql_real_escape_string($in_contact['email']);
        $contact->phone_number = mysql_real_escape_string($in_contact['phone_number']);
        if ($contact->update())
            return true;
    }

}
