<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing\Enums;

enum Scheme: string
{

	case Chrome = 'chrome';
	case ChromeExtension = 'chrome-extension';
	case Data = 'data';
	case Dns = 'dns';
	case Ftp = 'ftp';
	case Git = 'git';
	case Http = 'http';
	case Https = 'https';
	case Imap = 'imap';
	case MailTo = 'mailto';
	case Mvn = 'mvn';
	case OTPAuth = 'otpauth';
	case Pop = 'pop';
	case Sftp = 'sftp';
	case Ssh = 'ssh';
	case Svn = 'svn';
	case Tel = 'tel';
	case ViewSource = 'view-source';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Scheme::Chrome => 'Chrome Settings',
			Scheme::ChromeExtension => 'Chrome Extension Settings',
			Scheme::Data => 'Data String',
			Scheme::Dns => 'DNS',
			Scheme::Ftp => 'FTP',
			Scheme::Git => 'Git',
			Scheme::Http => 'HTTP',
			Scheme::Https => 'HTTPS',
			Scheme::Imap => 'IMAP',
			Scheme::MailTo => 'Mail To',
			Scheme::Mvn => 'Apache Maven',
			Scheme::OTPAuth => 'OTP Auth',
			Scheme::Pop => 'POP3',
			Scheme::Sftp => 'SFTP',
			Scheme::Ssh => 'SSH',
			Scheme::Svn => 'Subversion',
			Scheme::Tel => 'Phone',
			Scheme::ViewSource => 'View Source',
		};
	}

}