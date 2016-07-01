<?php

unlink('database/taskrouter.sqlite');
touch('database/taskrouter.sqlite');
print "Created database taskrouter.sqlite\n";
copy(".env.example", ".env");
print "Replaced enviroment variables for the project\n";