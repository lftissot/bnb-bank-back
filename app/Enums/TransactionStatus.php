<?php
namespace App\Enums;


abstract class TransactionStatus{
    const Pending = 0;
    const Approved = 1;
    const Denied = 2;
}