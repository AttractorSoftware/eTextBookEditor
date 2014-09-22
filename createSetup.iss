#define MyAppName "Ёлектронные учебники"
#define MyAppVersion "0.2.0-beta"
#define MyAppPublisher "IT-Attractor"
#define MyAppURL "http://www.it-attractor.com"
#define LaunchProgram "«апустить приложение после установки"
#define DesktopIcon "—оздать €рлык на рабочем столе?"
#define CreateDesktopIcon "—оздать €рлык"



[Setup]
AppId={{F3E30478-2D70-4CBC-AB4F-0B7A0A4D44AC}
AppName={#MyAppName}
AppVersion={#MyAppVersion}
AppPublisher={#MyAppPublisher}
AppPublisherURL={#MyAppURL}
AppSupportURL={#MyAppURL}
AppUpdatesURL={#MyAppURL}
DefaultDirName={pf}\{#MyAppName}
DefaultGroupName={#MyAppName}
Compression=lzma
SolidCompression=yes
OutputDir=F:\
OutputBaseFilename=ebook-setup-{#MyAppVersion}

[Languages]
Name: "russian"; MessagesFile: "compiler:Languages\Russian.isl"

[Files]
Source: "F:\node-webkit-win32-v0.10.4\*"; Excludes: "libEGL.dll,libGLESv2.dll" ; DestDir: "{app}"; Flags: ignoreversion recursesubdirs
Source: "F:\node-webkit-win32-v0.10.4\icon.ico"; DestDir: "{app}"; DestName: "icon.ico"; Flags: ignoreversion

[Tasks]
Name: "desktopicon"; Description: "{#CreateDesktopIcon}"; GroupDescription: "{#DesktopIcon}"

[Icons]
Name: "{group}\{#MyAppName}"; Filename: "{app}\ebook.exe"; WorkingDir: "{app}"; IconFilename: "{app}/icon.ico"
Name: "{userstartup}\{#MyAppName}"; Filename: "{app}\ebook.exe"; WorkingDir: "{app}"; IconFilename: "{app}/icon.ico"
Name: "{userdesktop}\{#MyAppName}"; Filename: "{app}\ebook.exe"; WorkingDir: "{app}"; IconFilename: "{app}/icon.ico"; Tasks: desktopicon

[Run]
Filename: "{app}\ebook.exe"; WorkingDir: "{app}"; Description: {#LaunchProgram}; Flags: postinstall shellexec