<?php
/**
 * Secure presenter
 */

namespace App\Presenters;

use Nette;

/**
 * Secure presenter class
 */
class SecurePresenter extends BasePresenter
{    
    /** přepsání funkcí pomocí traitu + validace přihlášení **/
    use \Security\TPermission;
}