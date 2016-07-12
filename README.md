# AlfredWorkflows
Workflows for Alfred 3

###GeneratePassword
Quick generate password in strong security.

```
Usage:        generatepassword length=32\\&type=number-lower-upper\\&splitChar=_\\&splitLength=4\\&times=5
length:       length of password
type:         password with which type of char, can be: number/lower/upper/special join by '-', or 'all' for all char and 'custom' for custom string
customStr:    you can use type=custom to custom string for you password just like customStr=123456, and it will create password by use 123456
splitChar:    split char for password, default is empty so that password just like connect one by one
splitLength:  split password by every splitLength step
times:        create how many password in one time
```
