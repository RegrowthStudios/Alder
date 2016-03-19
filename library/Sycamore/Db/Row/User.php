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

    namespace Sycamore\Db\Row;
    
    use Sycamore\Db\Row\AbstractObjectRow;
    
    /**
     * Row object representing user table rows.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class User extends AbstractObjectRow
    {
        // Admin/THIS User Token Required:
        public $name;
        public $preferredName;
        public $dateOfBirth;
        /// Password Required in case of THIS User Token:
        public $password;
        /// Verification Required (via JWT) in case of THIS User Token:
        public $primaryEmail;
        public $secondaryEmail;
        public $verified;
        /// Admin Action Required (DISALLOW THIS User Token - unless Admin):
        public $username;
        public $banned;
        // Uneditable in API:
        public $lastOnline;
        ///* Superusers can do anything and have complete rights (i.e. cannot be superseded by any ACL group).
        public $superUser;
    }