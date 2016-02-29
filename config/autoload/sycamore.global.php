<?php

/* 
 * Copyright (C) 2016 Matthew Marshall <matthew.marshall96@yahoo.co.uk>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

    /**
     * Global configuration file for the Sycamore application.
     */
    return array (
        "Sycamore" => array (
            "application_title" => "Example", // Title of the application.
            "attachment" => array ( /* Attachment Details */
                "directory" => APP_DIRECTORY . "/attachments/",
                "checkMimeType" => true,
            ),
            "cache" => array ( /* Cache Details */
                "namespace" => "sycamore_cache", // Namespace in which all application data is cached.
                "timeToLive" => 1800/*30 Mins*/, // How long does the data live for in cache?
            ),
            "cron" => array ( /* Cron Details */
                "directory" => APP_DIRECTORY . "/cron/", // Location where temporary cron files are stored.
            ),
            "db" => array ( /* Database Details */
                "driver" => "pdo_mysql", // The driver to use for database interfacing.
                "params" => array (
                    "host" => "localhost", // The host of the database.
                    "port" => "3306", // The port to connect to the database via.
                    "username" => "", // The username to connect to the database with.
                    "password" => "", // The password to connect to the database with.
                    "dbname" => "" // The name of the database.
                ),
                "tablePrefix" => "",
            ),
            "domain" => "example.com", // Domain of the application.
            "email" => array ( /* Email Details */
                "transport" => "smtp", // Which method to send emails via. SMTP reduces chances of email being treated as spam vs. Sendmail.
                "options" => array ( /* Transport Options */
                    "name" => "example.com", // The name of the SMTP server.
                    "host" => "127.0.0.1", // The host IP of the SMTP server.
                    "port" => 25, // Port to connect to. Usually 25 for insecure, 587 for TLS and 465 for SSL.
                    "connection" => array ( /* Connection Details */
                        "class" => "smtp", // The method of connecting to the SMTP server. Options: "smtp", "plain", "login", and "crammd5"
                        "username" => "", // Username of user on SMTP server.
                        "password" => "", // Password of user on SMTP server.
                        "ssl" => "", // SSL method to use.
                    )
                ),
            ),
            "language" => "en_GB", // The language of the application. (http://www.roseindia.net/tutorials/I18N/locales-list.shtml has a list of possible locales.)
            "newsletter" => array ( /* Newsletter Details */
                "name" => "John Smith", // The name to send newsletters from by default.
                "email" => "john.smith@example.com", // The email to send newsletters from by default.
                "attachmentDirectory" => APP_DIRECTORY . "/attachments/", // Location where temporary attachment files are stored.
            ),
            "security" => array ( /* Security Details */
                "enableClickjackingProtection" => true, // If true, prevents clickjacking attacks by not allowing site to be rendered in frame of another site.
                "password" => array ( /* Password Security Details */
                    "strictness" => "normal", // How secure must passwords be? Normal -> >8 characters long, include numbers and letters. High -> Normal + include capital letter. Strict -> High + include symbol. Retroactively checks passwords.
                    "hashingStrength" => 11, // How strong should the hash be? Higher is stronger, but requires more CPU time.
                    "minimumLength" => 8,
                    "maximumLength" => 48,
                ),
                "simpleHashAlgorithm" => "sha256", // The hashing algorithm to be used for simple hashes - no sensitive data is hashed using this.
                "sessionLength" => 43200/*12 Hours*/, // How long should log in session last if not extended?
                "sessionLengthExtended" => 2629740,/*~1 Month*/ // How long should an extended log-in session last?
                "tokenPrivateKey" => DEFAULT_VAL, // The key used for signing JWTs. Do NOT share. In case of asymmetric hash algorithm, this should be the file URI ("file://...") to the private RSA key file.
                "tokenPublicKey" => DEFAULT_VAL, // The key used for verifying JWTs IF an asymmetric hash algorithm is specified. In case of asymmetric hash algorithm, this should be the file URI ("file://...") to the public RSA key file.
                "tokenHashAlgorithm" => "HS256", // The hashing algorithm used for the signing of JWTs. Allowed values: HS256, HS384, HS512, RS256, RS384, RS512. HS values are Hmac symmetric key methods, RS values are asymmetric RSA public/private key methods.
                "verifyTokenLifetime" => 21600/*6 Hours*/ // How long does a verification token last?
            ),
            "username" => array ( /* Username Details */
                "minimumLength" => 1,
                "maximumLength" => 32,
            ),
        ),
    );
