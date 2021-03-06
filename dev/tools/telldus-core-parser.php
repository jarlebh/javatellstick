<?php 
// What does this do ?
// Parse telldus-core.h, return class object....
// We use this tool to generate our interface.

$telldus_core_h = <<<EOT

#ifndef TELLDUSCORE_H
#define TELLDUSCORE_H

// The following ifdef block is the standard way of creating macros
// which make exporting from a DLL simpler.  All files within this DLL
// are compiled with the TELLDUSCORE_EXPORTS symbol defined on the command line.
// This symbol should not be defined on any project that uses this DLL.
// This way any other project whose source files include this file see
// TELLSTICK_API functions as being imported from a DLL, whereas this DLL
// sees symbols defined with this macro as being exported.

#ifdef _WINDOWS
	#if defined(TELLDUSCORE_EXPORTS)
		#if defined(_CL64)
			#define TELLSTICK_API
		#else
			#define TELLSTICK_API __declspec(dllexport)
		#endif
	#else
		#define TELLSTICK_API __declspec(dllimport)
	#endif
	#define WINAPI __stdcall
#else
	#define WINAPI
	#define TELLSTICK_API
#endif

typedef void (WINAPI *TDDeviceEvent)(int deviceId, int method, const char *data, int callbackId, void *context);
typedef void (WINAPI *TDDeviceChangeEvent)(int deviceId, int changeEvent, int changeType, int callbackId, void *context); 
typedef void (WINAPI *TDRawDeviceEvent)(const char *data, int controllerId, int callbackId, void *context);

#ifndef __cplusplus 
	#define bool char 
#endif

#ifdef __cplusplus
extern "C" {
#endif
	TELLSTICK_API void WINAPI tdInit(void);
	TELLSTICK_API int WINAPI tdRegisterDeviceEvent( TDDeviceEvent eventFunction, void *context );
	TELLSTICK_API int WINAPI tdRegisterDeviceChangeEvent( TDDeviceChangeEvent eventFunction, void *context);
	TELLSTICK_API int WINAPI tdRegisterRawDeviceEvent( TDRawDeviceEvent eventFunction, void *context );
	TELLSTICK_API int WINAPI tdUnregisterCallback( int callbackId );
	TELLSTICK_API void WINAPI tdClose(void);
	TELLSTICK_API void WINAPI tdReleaseString(char *string);
	
	TELLSTICK_API int WINAPI tdTurnOn(int intDeviceId);
	TELLSTICK_API int WINAPI tdTurnOff(int intDeviceId);
	TELLSTICK_API int WINAPI tdBell(int intDeviceId);
	TELLSTICK_API int WINAPI tdDim(int intDeviceId, unsigned char level);
	TELLSTICK_API int WINAPI tdExecute(int intDeviceId);
	TELLSTICK_API int WINAPI tdUp(int intDeviceId);
	TELLSTICK_API int WINAPI tdDown(int intDeviceId);
	TELLSTICK_API int WINAPI tdStop(int intDeviceId);
	TELLSTICK_API int WINAPI tdLearn(int intDeviceId);
	TELLSTICK_API int WINAPI tdMethods(int id, int methodsSupported);
	TELLSTICK_API int WINAPI tdLastSentCommand( int intDeviceId, int methodsSupported );
	TELLSTICK_API char *WINAPI tdLastSentValue( int intDeviceId );
	
	TELLSTICK_API int WINAPI tdGetNumberOfDevices();
	TELLSTICK_API int WINAPI tdGetDeviceId(int intDeviceIndex);
	TELLSTICK_API int WINAPI tdGetDeviceType(int intDeviceId);
	
	TELLSTICK_API char * WINAPI tdGetErrorString(int intErrorNo);
	
	TELLSTICK_API char * WINAPI tdGetName(int intDeviceId);
	TELLSTICK_API bool WINAPI tdSetName(int intDeviceId, const char* chNewName);
	TELLSTICK_API char * WINAPI tdGetProtocol(int intDeviceId);
	TELLSTICK_API bool WINAPI tdSetProtocol(int intDeviceId, const char* strProtocol);
	TELLSTICK_API char * WINAPI tdGetModel(int intDeviceId);
	TELLSTICK_API bool WINAPI tdSetModel(int intDeviceId, const char *intModel);
	
	TELLSTICK_API char * WINAPI tdGetDeviceParameter(int intDeviceId, const char *strName, const char *defaultValue);
	TELLSTICK_API bool WINAPI tdSetDeviceParameter(int intDeviceId, const char *strName, const char* strValue);
	
	TELLSTICK_API int WINAPI tdAddDevice();
	TELLSTICK_API bool WINAPI tdRemoveDevice(int intDeviceId);
	
	TELLSTICK_API int WINAPI tdSendRawCommand(const char *command, int reserved);

	TELLSTICK_API void WINAPI tdConnectTellStickController(int vid, int pid, const char *serial);
	TELLSTICK_API void WINAPI tdDisconnectTellStickController(int vid, int pid, const char *serial);

#ifdef __cplusplus
}
#endif

//Device methods
#define TELLSTICK_TURNON	1
#define TELLSTICK_TURNOFF	2
#define TELLSTICK_BELL		4
#define TELLSTICK_TOGGLE	8
#define TELLSTICK_DIM		16
#define TELLSTICK_LEARN		32
#define TELLSTICK_EXECUTE	64
#define TELLSTICK_UP		128
#define TELLSTICK_DOWN		256
#define TELLSTICK_STOP		512

//Error codes
#define TELLSTICK_SUCCESS 0
#define TELLSTICK_ERROR_NOT_FOUND -1
#define TELLSTICK_ERROR_PERMISSION_DENIED -2
#define TELLSTICK_ERROR_DEVICE_NOT_FOUND -3
#define TELLSTICK_ERROR_METHOD_NOT_SUPPORTED -4
#define TELLSTICK_ERROR_COMMUNICATION -5
#define TELLSTICK_ERROR_CONNECTING_SERVICE -6
#define TELLSTICK_ERROR_UNKNOWN_RESPONSE -7
#define TELLSTICK_ERROR_UNKNOWN -99

//Device typedef
#define TELLSTICK_TYPE_DEVICE	1
#define TELLSTICK_TYPE_GROUP	2
#define TELLSTICK_TYPE_SCENE	3

//Device changes
#define TELLSTICK_DEVICE_ADDED			1
#define TELLSTICK_DEVICE_CHANGED		2
#define TELLSTICK_DEVICE_REMOVED		3
#define TELLSTICK_DEVICE_STATE_CHANGED	4

//Change types
#define TELLSTICK_CHANGE_NAME			1
#define TELLSTICK_CHANGE_PROTOCOL		2
#define TELLSTICK_CHANGE_MODEL			3
#define TELLSTICK_CHANGE_METHOD			4

#endif
EOT;


$file = explode("\n", $telldus_core_h);



$OUT = "
// Last generated ".date('r',time())."
public interface CLibrary extends Library {\n\n";
foreach($file as $f){
	
	// A method.
	if (preg_match("#TELLSTICK_API (.*?) WINAPI (.*?);#", $f, $matches)){
		$returntype = $matches[1];
		$method = $matches[2];
		
		$OUT .= "// ".$matches[0]."\n";
		$OUT .= "public ".$returntype." ".$method.";\n\n";
	}elseif (preg_match("#define (.*?)\t.*?(\d+)#", $f, $matches)){
		$constant = $matches[1];
		$value = $matches[2];
		
		$OUT .= "// ".$matches[0]."\n";
		$OUT .= "public final int $constant = $value;\n";
		
	}
	
	
	
}


echo $OUT;