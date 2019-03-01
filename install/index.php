<?php

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

Class invoicebox_payment extends CModule
{
	const MODULE_ID = 'invoicebox.payment';
	var $MODULE_ID 	= 'invoicebox.payment';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $strError = '';
	function __construct() {
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION 		= $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE 	= $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME 		= "InvoiceBox";
		$this->MODULE_DESCRIPTION 	= "InvoiceBox payment module for Bitrix CMS: \"Small business\", \"Business\" and \"Business web-cluster\".";   
		$this->PARTNER_NAME 		= "InvoiceBox";
		$this->PARTNER_URI 		= "https://www.invoicebox.ru";
	}

	//func
	function InstallEvents() {
		return true;
	}

	//func
	function UnInstallEvents() {
		return true;
	}

	//func
	function rmFolder($dir) {
		foreach(glob($dir . '/{,.description}*', GLOB_BRACE) as $file) {
			if (is_dir($file)) {
				$this->rmFolder($file);
			} else {
				unlink($file);
			}; //if
		}; //foreach
		rmdir($dir);
		return true;
	}

	//func
	function copyDir( $source, $destination ) {
		if ( is_dir( $source ) ) {
			@mkdir( $destination, 0755, true );
			$directory = dir( $source );

			while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
				if ( $readdirectory == '.' || $readdirectory == '..' ) {
					continue;
				}; //
				$PathDir = $source . '/' . $readdirectory; 
				if ( is_dir( $PathDir ) ) {
					$this->copyDir( $PathDir, $destination . '/' . $readdirectory );
					continue;
				}; //if
				copy( $PathDir, $destination . '/' . $readdirectory );
			};

			$directory->close();
		} else {
			copy( $source, $destination );
		};
	}

	//func
	function InstallFiles($arParams = array(), $alternativePath = false) {
		global $APPLICATION;

        $ipn_dir = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/sale_payment';
		// 1. Check folder existance
		if ( !is_dir( $ipn_dir ) ) {
			mkdir( $ipn_dir, 0755, true );
			if ( !is_dir( $ipn_dir ) ) {
                $this->strError = Loc::getMessage('SALE_HPS_INVOICEBOX_INSTALL_ACCESS') .
                    "/local/php_interface/include/sale_payment � /local/modules/sale/payment";
                return false;
			}; //
		}; //
		// 2. Create module folder
		$ipn_dir.= "/invoicebox";
		if ( !is_dir( $ipn_dir ) ) {
			mkdir( $ipn_dir, 0755, true );
			if ( !is_dir( $ipn_dir ) ) {
                $this->strError = Loc::getMessage('SALE_HPS_INVOICEBOX_INSTALL_ACCESS') .
                    "/local/php_interface/include/sale_payment � /local/modules/sale/payment";
                return false;
			}; //
		}; //if
		// 3. Create notification page folder
		$not_dir = $_SERVER['DOCUMENT_ROOT'].'/personal/order/payment/invoicebox';
		if ( !is_dir($not_dir) ) {
			mkdir($not_dir, 0755, true);
			if ( !is_dir($not_dir) ) {
				$this->strError = Loc::getMessage('SALE_HPS_INVOICEBOX_INSTALL_ACCESS') .
						"/local/php_interface/include/sale_payment � /local/modules/sale/payment";
					return false;
			}; //
		}; //if
		// 5. Install module files
		$source = $_SERVER['DOCUMENT_ROOT'] . '/local/modules/'.self::MODULE_ID.'/install';
		if ( is_dir( $source ) ) {
            $this->copyDir( $source."/sale_payment/invoicebox", $ipn_dir );
            $this->copyDir( $source."/notifications/invoicebox", $not_dir );
            $this->copyDir($source.'/components', $_SERVER['DOCUMENT_ROOT'].'/local/components');

            if ( !file_exists( $ipn_dir . "/handler.php" ) ) {
				$this->strError = Loc::getMessage('SALE_HPS_INVOICEBOX_INSTALL_ACCESS') .
						"/local/php_interface/include/sale_payment � /local/modules/sale/payment";
					$this->UnInstallFiles();
				return false;
			}; //
			if ( !file_exists( $not_dir . "/notification_invoicebox.php" ) ) {
				$this->strError = Loc::getMessage('SALE_HPS_INVOICEBOX_INSTALL_ACCESS') .
						"/local/php_interface/include/sale_payment � /local/modules/sale/payment";
					$this->UnInstallFiles();
				return false;
			}; //
		} else {
			$this->strError =
				Loc::getMessage('SALE_HPS_INVOICEBOX_INSTALL_COPY') . $source;
			return false;
		}; //if
		return true;
	}

	//func
	function UnInstallFiles() {
		$this->rmFolder($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/sale_payment/invoicebox');
        $this->rmFolder($_SERVER['DOCUMENT_ROOT'].'/personal/order/payment/invoicebox');
        $this->rmFolder($_SERVER['DOCUMENT_ROOT'].'/local/components/invoicebox');
        return true;
	}

	//func
	function DoInstall() {
		global $APPLICATION;
		if ( $this->InstallFiles() ) {
			RegisterModule(self::MODULE_ID);
		} else {
			$APPLICATION->throwException($this->strError);
		}; //
	}

	//func
	function DoUninstall() {
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallFiles();
	} //func
}; //class