<?php
require_once('fpdm.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = array(
        'name' => $_POST['name'],
        'ic' => $_POST['ic'],
        'dob' => $_POST['dob'],
        'address' => $_POST['address'],
        'phone' => $_POST['phone'],
        'email' => $_POST['email']
    );

    $pdf = new FPDM('../APPROVED JCS MEMBERSHIP FORM.pdf');
    $pdf->Load($fields);
    $pdf->Merge();
    $pdf->Output('filled_membership_form.pdf', 'D');
} else {
    echo 'Invalid request';
}
?>