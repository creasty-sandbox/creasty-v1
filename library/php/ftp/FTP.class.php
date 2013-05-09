<?php
/*	FTP CONNECTION
	$Id: FTP.class.php, v2.47 2009/03/31 19:03:22 ykiwng Exp $
----------------------------------------------------------------------------------------------------
	Copyright 2009 Creasty Systems.
	All rights reserved.
----------------------------------------------------------------------------------------------------
	See more information, visit www.creasty.com/freeprojects/php/ftp/
----------------------------------------------------------------------------------------------------*/


/*////////////////////////////////////////////////////
@Class: FTP

@Author: ykiwng

@Useage:
> $conn=new FTP('ftp://sample:pass123@ftp.example.com/public_html/');
> print $conn->getCurrentDirName();
> $conn->disconnect();
>>> /public_html
////////////////////////////////////////////////////*/
class FTP{
	var $stream; // The link identifier of the FTP connection.
	
	/*////////////////////////////////////////////////////
	@Method: FTP (Constractor)
	
	@Function:
	Opens an FTP connection
	
	@Properties:
	$uri
		ftp://username:password@server:port/currentdir/
	
	$ssl (optional) - Open connection as an Secure SSL-FTP
	$timeout (optional) - This parameter specifies the timeout for all subsequent network operations.
		If omitted, the default value is 90 seconds.
		The timeout can be changed and queried at any time with ftp_set_option() and ftp_get_option().
	////////////////////////////////////////////////////*/
	function FTP($uri,$ssl=false,$timeout=90){
		preg_match('/ftp:\/\/(.*?):(.*?)@(.*?)(\/.*)/i',$uri,$match);
		list($host,$port)=split(':',$match[3]);
		$port=$port?$port:21;
		
		if($ssl){
			$conn=@ftp_ssl_connect($host,$port,$timeout);
		}else{
			$conn=@ftp_connect($host,$port,$timeout);
		}
		if($conn){
			if(@ftp_login($conn,$match[1],$match[2])){
				ftp_chdir($conn,$match[4]);
				$this->stream=$conn;
			}else{
				print '[FTP] ERROR: Couldn\'t login to FTP Server';
				die();
			}
		}else{
			print '[FTP] ERROR: Couldn\'t connect to FTP Server';
			die();
		}
		
		return false;
	}
	
	/*////////////////////////////////////////////////////
	@Method: disconnect
	
	@Function:
	Closes an FTP connection
	////////////////////////////////////////////////////*/
	function disconnect(){
		return @ftp_close($this->stream);
	}
	
	
	/*////////////////////////////////////////////////////
	@Method: allocate
	
	@Function:
	Allocates space for a file to be uploaded
	
	@Properties:
	$filesize - The number of bytes to allocate.
	&$result (optional) - A textual representation of the servers response.
	
	@Returns
	Returns TRUE on success or FALSE on failure.
	////////////////////////////////////////////////////*/
	function allocate($filesize,&$result=null){
		if($result){
			return ftp_alloc($this->stream,$filesize,$result);
		}else{
			return ftp_alloc($this->stream,$filesize);
		}
	}
	
	/*////////////////////////////////////////////////////
	@Method: changeParentDir
	
	@Function:
	Changes to the parent directory
	
	@Returns
	Returns TRUE on success or FALSE on failure.
	////////////////////////////////////////////////////*/
	function changeParentDir(){
		return ftp_cdup($this->stream);
	}
	
	/*////////////////////////////////////////////////////
	@Method: setPermission
	
	@Function:
	Sets the permissions on the specified remote file.
	
	@Properties:
	$mode - The new permissions, given as an "octal" value.
	$remoteFile - The remote file.
	
	@Return values:
	Returns the new file permissions on success or FALSE on error.
	////////////////////////////////////////////////////*/
	function setPermission($mode,$remoteFile){
		return ftp_chmod($this->stream,$mode,$remoteFile);
	}
	
	/*////////////////////////////////////////////////////
	@Method: remove
	
	@Function:
	Deletes a file on the FTP server.
	
	@Properties:
	$path - The file or the directory to delete.
	
	@Returns:
	Returns TRUE on success or FALSE on failure.
	////////////////////////////////////////////////////*/
	function remove($path){
		if($path[strlen($path)-1]=='/'){
			return ftp_rmdir($this->stream,$path);
		}else{
			return ftp_delete($this->stream,$path);
		}
	}
	
	/*////////////////////////////////////////////////////
	@Method: exec
	
	@Function:
	Requests execution of a command on the FTP server.
	
	@Properties:
	$command - The command to execute.
	
	@Return values:
	Returns TRUE if the command was successful (server sent response code: 200);
	otherwise returns FALSE.
	////////////////////////////////////////////////////*/
	function exec($command){
		return ftp_exec($this->stream,$command);
	}
	
	/*////////////////////////////////////////////////////
	@Method: fileDownload
	
	@Function:
	Downloads a file from the FTP server.
	and saves it into a local file if <localFile> is provided, or saves to an open file.
	
	@Properties:
	handle - An open file pointer in which we store the data.
	localFile - The local file path (will be overwritten if the file already exists).
	
	remoteFile - The remote file path.
	mode - The transfer mode. Must be either FTP_ASCII or FTP_BINARY.
	startAt (optional) - The position in the remote file to start downloading from.
	
	$nonblocking - use non-blocking mode
	
	@Returns:
	Returns TRUE on success or FALSE on failure.
	////////////////////////////////////////////////////*/
	function fileDownload($parameters,$nonblocking=false){
		$handle=$parameters['handle'];
		$localFile=$parameters['localFile'] or null;
		
		$remoteFile=$parameters['remoteFile'];
		$mode=$parameters['mode'];
		$startAt=$parameters['startAt'] or 0;
		
		if($localFile){
			if($nonblocking){
				return ftp_nb_fget($this->stream,$handle,$remoteFile,$mode,$startAt);
			}else{
				return ftp_fget($this->stream,$handle,$remoteFile,$mode,$startAt);
			}
		}else{
			if($nonblocking){
				return ftp_nb_get($this->stream,$localFile,$remoteFile,$mode,$startAt);
			}else{
				return ftp_get($this->stream,$localFile,$remoteFile,$mode,$startAt);
			}
		}
	}
	
	/*////////////////////////////////////////////////////
	@Method: fileUpload
	
	@Function:
	Uploads from an open file to the FTP server if <localFile> is provided,
	or Uploads a file to the FTP server.
	
	@Properties:
	handle - An open file pointer on the local file. Reading stops at end of file.
	localFile - The local file path.
	
	remoteFile - The remote file path.
	mode - The transfer mode. Must be either FTP_ASCII or FTP_BINARY.
	startAt (optional) - The position in the remote file to start uploading from.
	
	$nonblocking - use non-blocking mode
	
	@Returns:
	Returns TRUE on success or FALSE on failure.
	////////////////////////////////////////////////////*/
	function fileUpload($parameters,$nonblocking=false){
		$handle=$parameters['handle'];
		$localFile=$parameters['localFile'] or null;
		
		$remoteFile=$parameters['remoteFile'];
		$mode=$parameters['mode'];
		$startAt=$parameters['startAt'] or 0;
		
		if($localFile){
			if($nonblocking){
				return ftp_nb_fput($this->stream,$handle,$remoteFile,$mode,$startAt);
			}else{
				return ftp_fput($this->stream,$handle,$remoteFile,$mode,$startAt);
			}
		}else{
			if($nonblocking){
				return ftp_nb_put($this->stream,$localFile,$remoteFile,$mode,$startAt);
			}else{
				return ftp_put($this->stream,$localFile,$remoteFile,$mode,$startAt);
			}
		}
	}
	
	/*////////////////////////////////////////////////////
	@Method: getBehaviours
	
	@Function:
	Retrieves various runtime behaviours of the current FTP stream.
	
	@Properties:
	$option
		FTP_TIMEOUT_SEC   Returns the current timeout used for network related operations.
		FTP_AUTOSEEK      Returns TRUE if this option is on, FALSE otherwise.
	
	@Returns:
	Returns the value on success or FALSE if the given $option is not supported.
	In the latter case, a warning message is also thrown.
	////////////////////////////////////////////////////*/
	function getBehaviours($handle,$option){
		return ftp_get_option($this->stream,$option);
	}
	
	/*////////////////////////////////////////////////////
	@Method: getModifiedTime
	
	@Function:
	Returns the last modified time of the given file.
	
	@Properties:
	$remoteFile - The file from which to extract the last modification time.
	
	@Return values:
	Returns the last modified time as a Unix timestamp on success, or -1 on error.
	////////////////////////////////////////////////////*/
	function getModified($remoteFile){
		return ftp_mdtm($this->stream,$remoteFile);
	}
	
	/*////////////////////////////////////////////////////
	@Method: createDir
	
	@Function:
	Create a directory.
	
	@Properties:
	$directory - The name of the directory that will be created.
	
	@Return values:
	Returns the newly created directory name on success or FALSE on error.
	////////////////////////////////////////////////////*/
	function createDir($directory){
		return ftp_mkdir($this->stream,$directory);
	}
	
	/*////////////////////////////////////////////////////
	@Method: nbContinue
	
	@Function:
	Continues retrieving/sending a file (non-blocking)
	
	@Return values:
	Returns FTP_FAILED or FTP_FINISHED or FTP_MOREDATA.
	////////////////////////////////////////////////////*/
	function nbContinue(){
		return ftp_nb_continue($this->stream);
	}
	
	/*////////////////////////////////////////////////////
	@Method: getList
	
	@Function:
	Returns a list of files in the given directory.
	Or returns a detailed list of files in the given directory if $raw is provided.
	
	@Properties:
	$directory - The directory path.
		[if $raw is provided]
		The directory to be listed.
		This parameter can also include arguments, eg. ftp_nlist($conn_id, "-la /your/dir");
		Note that this parameter isn't escaped so there may be some issues with filenames containing spaces and other characters.
	
	@Return values:
	Returns an array of filenames from the specified directory on success or FALSE on error.
	////////////////////////////////////////////////////*/
	function getList($directory,$raw=false,$recursive=false){
		if($raw){
			return ftp_rawlist($this->stream,$directory,$recursive);
		}else{
			return ftp_nlist($this->stream,$directory);
		}
		
	}
	
	/*////////////////////////////////////////////////////
	@Method: setPassiveMode
	
	@Function:
	Turns passive mode on or off.
	
	@Properties:
	$pasv - If TRUE, the passive mode is turned on, else it's turned off.
	
	@Returns:
	Returns TRUE on success or FALSE on failure.
	////////////////////////////////////////////////////*/
	function setPassiveMode($pasv){
		return ftp_pasv($this->stream,$pasv);
	}
	
	/*////////////////////////////////////////////////////
	@Method: getCurrentDirName
	
	@Function:
	Changes to the parent directory
	////////////////////////////////////////////////////*/
	function getCurrentDirName(){
		return ftp_pwd($this->stream);
	}
	
	/*////////////////////////////////////////////////////
	@Method: commandRAW
	
	@Function:
	Sends an arbitrary command to an FTP server.
	
	@Properties:
	$command - The command to execute.
	
	@Return values:
	Returns the server's response as an array of strings.
	No parsing is performed on the response string, nor does this function determine if the command succeeded.
	////////////////////////////////////////////////////*/
	function commandRAW($command){
		return ftp_raw($this->stream,$command);
	}
	
	/*////////////////////////////////////////////////////
	@Method: rename
	
	@Function:
	Renames a file or a directory.
	
	@Properties:
	$oldname - The old file/directory name.
	$newname - The new name.
	
	@Returns:
	Returns TRUE on success or FALSE on failure.
	////////////////////////////////////////////////////*/
	function rename($oldname,$newname){
		return ftp_rename($this->stream,$oldname,$newname);
	}
	
	/*////////////////////////////////////////////////////
	@Method: setRuntimeOption
	
	@Function:
	Set miscellaneous runtime FTP options.
	
	@Properties:
	$option
		FTP_TIMEOUT_SEC   Returns the current timeout used for network related operations.
		FTP_AUTOSEEK      Returns TRUE if this option is on, FALSE otherwise.
	
	$value - This parameter depends on which option  is chosen to be altered.
	
	@Returns:
	Returns TRUE if the option could be set; FALSE if not.
	A warning message will be thrown if the option is not supported or 
	the passed value doesn't match the expected value for the given option.
	////////////////////////////////////////////////////*/
	function setOption($option,$value){
		return ftp_set_option($this->stream,$option,$value);
	}
	
	/*////////////////////////////////////////////////////
	@Method: commandSITE
	
	@Function:
	Sends a SITE command to the server.
	
	@Properties:
	$command - The SITE command.
		Note that this parameter isn't escaped so there may be some issues with filenames containing spaces and other characters.
	
	@Returns:
	Returns TRUE on success or FALSE on failure.
	////////////////////////////////////////////////////*/
	function commandSITE($command){
		return ftp_site($this->stream,$command);
	}
	
	/*////////////////////////////////////////////////////
	@Method: getFileSize
	
	@Function:
	Returns the size of the given file.
	
	@Properties:
	$remoteFile - The remote file.
	
	@Return values:
	Returns the file size on success, or -1 on error.
	////////////////////////////////////////////////////*/
	function getFileSize($remoteFile){
		return ftp_size($this->stream,$remoteFile);
	}
	
	/*////////////////////////////////////////////////////
	@Method: getSystemType
	
	@Function:
	Returns the system type identifier of the remote FTP server
	
	@Return values:
	Returns the remote system type, or FALSE on error.
	////////////////////////////////////////////////////*/
	function getSystemType(){
		return ftp_systype($this->stream);
	}
	
	/*////////////////////////////////////////////////////
	@Method: isDirectory
	
	@Function:
	Returns if the path is a directory or not
	
	@Return values:
	Booldean
	////////////////////////////////////////////////////*/
	function isDirectory($directory){
		$origin=ftp_pwd($this->stream); // Get the current working directory
		
		if(@ftp_chdir($this->stream,$directory)){ // Attempt to change directory, suppress errors
			ftp_chdir($this->stream,$origin); // If the directory exists, set back to origin
			return true;
		}
		
		return false;
	}
	
}
?>