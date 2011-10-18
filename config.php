<?php
ini_set('display_errors', True); error_reporting(E_ALL);

Config::write(array(

  'mysql' => array(
    'username' => 'root',
    'password' => 'qS8crpsJ',
    'host' => 'localhost',
    'database' => 'athena'
  ),

  'domain' => 'hostdep',
  'snapbill-domain' => 'snap',
  'snapbill-ssl' => False,

  'developer' => True,

  'openssl' => array(
    'private-key'=>'
-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQDkW80PWHQXkrf6+LGHU+fiAnCgNQ2TUQonJOuVSh+eXi5euUWe
8o5nagQxqCO7EmN+NnlG9R4Rmi7YymslvZl04oa5OUAO6Ld8JT12TPfAnI+23bBk
Lb4Idft7qySVFqVWoiNObLC6xaZKlTgQ3NGlmnePGHuDRkSSrbl4n0yAZwIDAQAB
AoGBALSkliN0gml/a8DB6JW33zAfY/6n2TRXICP1BdNUDo0VzbKq9xMUp2fRKr4p
VxKOKlxWNTvXfVDJWhOulHIHeRLJ6YRrwM/hggASOGok2fveTjZtL9fQQHaun7hI
jmK58Fq8jSfvXgmswvo5jevVCodfCn2H5Sm6Td4d7eYYJ8bZAkEA9Swl2qBYzMRW
Klab3ubfj+t5WcXcBWCPr+0dusOs07WXKByN+pf/XBfFPm9COwFwz03HI6XF6XT5
4YeLqYl4VQJBAO5xj3KRNxpEO28+Rgb/6d9EGVo6dAKeJ59TVNExHbVok78PWKvc
fk5FaLsKI8XktOqtmQijQ/W4+9b+i7dKwcsCQADQxsd1ZRY5SPgXFammJvQ5mku8
JsE10wSIy2KFqBuELR6LCcXdn5HU2mkcwaGknZVy8sihkoj2RKaFZueHd4kCQQCK
C2jXFUdnh5U5RD5akxKdVdmvqSO82D9sOFxkeCERA6h19raJWTAutHR7xNUNHum2
7CYrIAqaWixDPj85MKidAkBPhw6oPMmwU8Epl+lZcXdoNyVAjdWjbqdrOZg1odnt
XiiasGwcWNlmLxgjEYQMDWmsDAy2nApNMNRjwH/A1PeY
-----END RSA PRIVATE KEY-----',
    'public-key'=>'
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDkW80PWHQXkrf6+LGHU+fiAnCg
NQ2TUQonJOuVSh+eXi5euUWe8o5nagQxqCO7EmN+NnlG9R4Rmi7YymslvZl04oa5
OUAO6Ld8JT12TPfAnI+23bBkLb4Idft7qySVFqVWoiNObLC6xaZKlTgQ3NGlmneP
GHuDRkSSrbl4n0yAZwIDAQAB
-----END PUBLIC KEY-----'
  ),

  'mixpanel-token' => 'ce2c444bc6930bff604de7a6b7c76aa8'
));

