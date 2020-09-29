<?php
// $user::create()

// Create
   // $username = 'ebuka' . rand(0,9);
   // $password = time();

   // if ( $user->create([
   //    'username' => $username,
   //    'password' => $password,
   //    'permissions' => "*"
   // ]) == true ) {
   //    echo "successful: $username - $password";
   // } else {
   //    echo 'failed';
   // };
// 

// Update
   // $username = 'ebuka' . rand(0,9);
   // $password = time();

   // if ( $user->update([
   //    'username' => $username,
   //    'password' => $password,
   //    'permissions' => "*"
   // ], "WHERE username = '' OR 1 = 1") == true ) {
   //    echo "successful: $username - $password";
   // } else {
   //    echo 'failed';
   // };
//

// Delete
   // if ($user->delete("WHERE 1")) {
   //    echo "successful " . $user->rowsAffected();
   // } else {
   //    echo "failed";
   // }
//

// function join

// INNER
// SELECT column_name(s)
// FROM table1
// INNER JOIN table2
// ON table1.column_name = table2.column_name;

// LEFT
// SELECT column_name(s)
// FROM table1
// LEFT JOIN table2
// ON table1.column_name = table2.column_name;

// RIGHT
// SELECT column_name(s)
// FROM table1
// RIGHT JOIN table2
// ON table1.column_name = table2.column_name;

// FULL
// SELECT column_name(s)
// FROM table1
// FULL OUTER JOIN table2
// ON table1.column_name = table2.column_name
// WHERE condition;

// SELF
// SELECT column_name(s)
// FROM table1 T1, table1 T2
// WHERE condition;

// UNION
// SELECT column_name(s) FROM table1
// UNION
// SELECT column_name(s) FROM table2;