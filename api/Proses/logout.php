<?php
require_once __DIR__ . '/../Server/auth.php';  // sesuaikan path relatifnya
auth_session();

// Simpan di: api/Proses/logout.php

auth_clear();

header("Location: ../login.php");
exit();
