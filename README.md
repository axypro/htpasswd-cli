# axy\htpasswd-cli

* GitHub: [axypro/htpasswd-cli](https://github.com/axypro/htpasswd-cli)
* Composer: [axy/htpasswd-cli](https://packagist.org/packages/axy/htpasswd-cli)

[Documentation in Russian](https://github.com/axypro/htpasswd-cli/wiki).

PHP 5.4+

Library does not require any dependencies (except composer packages).

Provides console utility `axy-htpasswd` that is analogue of Apache `htpasswd` utility.

Install (via composer):

```
$ composer global require axy/htpasswd-cli
```

The utility is stored in the directory `.composer/vendor/bin`.
If this directory has been added to `$PATH` you can use the utility from the command line:

```
$ axy-htpasswd -cb .password-file nick password
```

You can rename `axy-htpasswd` to `htpasswd` and use as usual.

### Algorithms

`axy-password` supports all crypt algorithms of Apache 2.4 htpasswd:

* BCrypt
* MD5 (Apache version)
* SHA1
* CRYPT
* PLAIN TEXT

### Interface

`axy-password` matches the interface of `htpasswd`:

```
Usage:
	axy-htpasswd [-cimBdpsDv] [-C cost] passwordfile username
	axy-htpasswd -b[cmBdpsDv] [-C cost] passwordfile username password

	axy-htpasswd -n[imBdps] [-C cost] username
	axy-htpasswd -nb[mBdps] [-C cost] username password
 -c  Create a new file. (no effect in the current version)
 -n  Don't update file; display results on stdout.
 -b  Use the password from the command line rather than prompting for it.
 -i  Read password from stdin without verification (for script usage).
 -m  Force MD5 encryption of the password (default).
 -B  Force bcrypt encryption of the password (very secure).
 -C  Set the computing time used for the bcrypt algorithm
     (higher is more secure but slower, default: 5, valid: 4 to 31).
 -d  Force CRYPT encryption of the password (8 chars max, insecure).
 -s  Force SHA encryption of the password (insecure).
 -p  Do not encrypt the password (plaintext, insecure).
 -D  Delete the specified user.
 -v  Verify password for the specified user.
On other systems than Windows and NetWare the '-p' flag will probably not work.
The SHA algorithm does not use a salt and is less secure than the MD5 algorithm.
```

Differs:

* The option `-c` has no effect. If the file exists then it will be changed. If not exist then will be created.
* Many errors of options combination are ignored.
* The option `-n` can be used together with `-D` and `-v`. It is analogue of an empty file, the user will not found.

### Program API

For program API (PHP) see [axypro/htpasswd](https://github.com/axypro/htpasswd).
