<?php
require __DIR__ . '/../Server/auth.php';  // sesuaikan path relatifnya
auth_session();

// Simpan di: api/Proses/logout.php

require __DIR__ . '/../Server/auth.php';

auth_clear();

header("Location: ../login.php");
exit();
