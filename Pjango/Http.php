<?php
use Pjango\Http\ResponseRedirect;

function HttpResponseRedirect($redirect_to) {
    $httpRes = new ResponseRedirect($redirect_to);
    exit();
}