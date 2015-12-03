<?php
/**
 * @package axy\htpasswd\cli
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\htpasswd\cli;

use axy\cli\bin\Task;
use axy\htpasswd\PasswordFile;

/**
 * htpasswd command
 */
class Htpasswd extends Task
{
    const ERROR_ACCESS = 1;
    const ERROR_ARGS = 2;
    const ERROR_VERIFY = 3;
    const ERROR_INTERRUPT = 4;
    const ERROR_LENGTH = 5;
    const ERROR_USERNAME = 6;
    const ERROR_FORMAT = 7;

    /**
     * {@inheritdoc}
     */
    protected function loadOpts()
    {
        if (!($this->loadFilename() && $this->loadUser() && $this->loadPassword())) {
            if ($this->io->getStatus() === 0) {
                $this->usage();
            }
            return false;
        }
        if ($this->opts->getNextArgument() !== null) {
            $this->usage();
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function process()
    {
        $this->file = new PasswordFile($this->filename);
        if ($this->opts->getOption('D')) {
            $this->delete();
        } elseif ($this->opts->getOption('v')) {
            $this->verify();
        } else {
            $this->add();
        }
    }

    private function add()
    {
        $aOptions = null;
        if ($this->opts->getOption('B')) {
            $alg = PasswordFile::ALG_BCRYPT;
            $c = $this->opts->getOption('C');
            if ($c !== null) {
                $c = (int)$c;
                if (($c < 4) || ($c > 31)) {
                    $this->error('argument to -C must be a positive integer from 4 to 31', self::ERROR_ARGS);
                    return;
                }
                $aOptions['cost'] = $c;
            }
        } elseif ($this->opts->getOption('d')) {
            $alg = PasswordFile::ALG_CRYPT;
        } elseif ($this->opts->getOption('s')) {
            $alg = PasswordFile::ALG_SHA1;
        } elseif ($this->opts->getOption('p')) {
            $alg = PasswordFile::ALG_PLAIN;
        } else {
            $alg = PasswordFile::ALG_MD5;
        }
        if ($this->file->setPassword($this->user, $this->password, $alg, $aOptions)) {
            $this->out('Adding password for user '.$this->user);
        } else {
            $this->out('Updating password for user '.$this->user);
        }
        $this->save();
    }

    private function delete()
    {
        if ($this->file->remove($this->user)) {
            $this->error('Deleting password for user '.$this->user);
        } else {
            $this->error('User '.$this->user.' not found');
        }
        $this->save();
    }

    private function verify()
    {
        if ($this->file->isUserExist($this->user)) {
            if ($this->file->verify($this->user, $this->password)) {
                $this->error('Password for user a correct.');
            } elseif ($this->file->isUserExist($this->user)) {
                $this->error('password verification failed');
            }
        } else {
            $this->error('User '.$this->user.' not found');
        }
    }

    private function save()
    {
        if (!$this->filename) {
            return $this->out($this->file->getContent());
        }
        if ($this->checkWritable($this->filename)) {
            $this->file->save();
        } else {
            $this->error('cannot create file ' . $this->filename, self::ERROR_ACCESS);
        }
    }

    /**
     * @param string $fn
     * @return bool
     */
    private function checkWritable($fn)
    {
        if (is_file($fn)) {
            return is_writable($fn);
        }
        return is_writable(dirname($fn));
    }

    /**
     * @return bool
     */
    private function loadFilename()
    {
        if ($this->opts->getOption('n')) {
            return true;
        }
        $this->filename = $this->opts->getNextArgument();
        return ($this->filename !== null);
    }

    /**
     * @return bool
     */
    private function loadUser()
    {
        $this->user = $this->opts->getNextArgument();
        return ($this->user !== null);
    }

    /**
     * @return bool
     */
    private function loadPassword()
    {
        if ($this->opts->getOption('D')) {
            return true;
        }
        if ($this->opts->getOption('b')) {
            $this->password = $this->opts->getNextArgument();
        } elseif ($this->opts->getOption('i')) {
            $this->password = $this->readLine();
        } else {
            $password = $this->silentRead('New password: ', true);
            $repeat = $this->silentRead('Re-type new password: ', true);
            if ($password !== $repeat) {
                $this->error('password verification error', self::ERROR_VERIFY);
                return false;
            }
            $this->password = $password;
        }
        return ($this->password !== null);
    }

    /**
     * {@inheritdoc}
     */
    protected $usageStatusExit = self::ERROR_ARGS;

    /**
     * {@inheritdoc}
     */
    protected $optionsFormat = [
        'c' => false,
        'n' => false,
        'b' => false,
        'i' => false,
        'm' => false,
        'B' => false,
        'd' => false,
        's' => false,
        'p' => false,
        'D' => false,
        'v' => false,
        'C' => true,
    ];

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var \axy\htpasswd\PasswordFile
     */
    private $file;
}
