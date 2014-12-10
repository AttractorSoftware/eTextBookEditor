#define MyAppName "Электронные учебники"
#define MyAppVersion "1.3.0"
#define MyAppPublisher "IT-Attractor"
#define MyAppURL "http://www.it-attractor.com"
#define LaunchProgram "Запустить приложение"
#define DesktopIcon "Создать ярлык на рабочем столе?"
#define CreateDesktopIcon "Создать ярлык"



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
Source: "F:\node-webkit-win32-v0.10.4\*"; Excludes: "createSetup.iss, README.md, app\img\Thumbs.db, app\img\Thumbs.db:encryptable, .gitignore" ; DestDir: "{app}"; Flags: ignoreversion recursesubdirs
Source: "F:\node-webkit-win32-v0.10.4\icon.ico"; DestDir: "{app}"; DestName: "icon.ico"; Flags: ignoreversion

[Tasks]
Name: "desktopicon"; Description: "{#CreateDesktopIcon}"; GroupDescription: "{#DesktopIcon}"

[Icons]
Name: "{group}\{#MyAppName}"; Filename: "{app}\ebook.exe"; WorkingDir: "{app}"; IconFilename: "{app}/icon.ico"
Name: "{group}\Удалить приложение"; Filename: "{app}\unins000.exe"; WorkingDir: "{app}"
Name: "{userstartup}\{#MyAppName}"; Filename: "{app}\ebook.exe"; WorkingDir: "{app}"; IconFilename: "{app}/icon.ico"
Name: "{userstartup}\Удалить приложение"; Filename: "{app}\unins000.exe"; WorkingDir: "{app}"
Name: "{userdesktop}\{#MyAppName}"; Filename: "{app}\ebook.exe"; WorkingDir: "{app}"; IconFilename: "{app}/icon.ico"; Tasks: desktopicon

[Run]
Filename: "{app}\ebook.exe"; WorkingDir: "{app}"; Description: {#LaunchProgram}; Flags: postinstall shellexec