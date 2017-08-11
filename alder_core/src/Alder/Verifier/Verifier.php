<?php

    namespace Alder\Verifier;

    class Verifier
    {
        const NO_MANIFEST = "no_manifest";
        const NOT_VALID   = "not_valid";
        const VALID       = "valid";

        public static function verifyAllInDir(string $directory, string $manifest = "files.json") : array {
            $results = [];

            foreach (\DirectoryIterator(file_build_path($directory, "data")) as $file) {
                if (!$file->isDir()) continue;

                $name = $file->getBasename();
                $manifestFilepath = file_build_path($file->getPathname(), $manifest);

                if (!file_exists($manifestFilepath)) {
                    $results[$name]["result"] = NO_MANIFEST;
                    continue;
                }

                $results[$name]["result"] = VALID;

                $manifestData = json_decode(file_get_contents($manifestFilepath));

                foreach ($manifestData as $filepathPart => $hash) {
                    $filepath = file_build_path($directory, $filepathPart);
                    if (!file_exists($filepath)) {
                        $results[$name]["result"] = NOT_VALID;
                        $results[$name]["files"][$filepath] = NOT_VALID;
                        continue;
                    }

                    if ($hash == md5(file_get_contents($filepath))) {
                        $results[$name]["files"][$filepath] = VALID;
                    } else {
                        $results[$name]["result"] = NOT_VALID;
                        $results[$name]["files"][$filepath] = NOT_VALID;
                    }
                }
            }
        }
    }
