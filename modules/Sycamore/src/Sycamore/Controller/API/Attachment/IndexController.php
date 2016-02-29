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

    namespace Sycamore\Controller\API\Attachment;
    
    use Sycamore\ErrorManager;
    use Sycamore\Attachment\AttachmentManager;
    use Sycamore\Controller\Controller;
    use Sycamore\Enums\ActionState;
    use Sycamore\Row\Attachment;
    use Sycamore\Utils\APIData;
    use Sycamore\Utils\TableCache;
    
    /**
     * Controller for handling attachments.
     */
    class IndexController extends Controller
    {
        /**
         * Executes the process of acquiring desired attachments.
         */
        public function indexAction()
        {
            // Attempt to acquire the provided data.
            $dataJson = $this->request->getQuery("data");
            
            // Grab the attachment table.
            $attachmentTable = TableCache::getTableFromCache("Attachment");
            
            // Fetch attachments with given values, or all attachments if no values were provided.
            $result = NULL;
            if (!$dataJson) {
                $result = $attachmentTable->fetchAll();
            } else {
                // Fetch only users matching given data.
                $data = APIData::decode($dataJson);
                $ids = (isset($data["ids"]) ? $data["ids"] : NULL);
                
                if (!is_null($ids)) {
                    $result = $attachmentTable->getByIds($ids);
                }
            }
            
            // If result is bad, input must have been bad.
            if (is_null($result)) {
                ErrorManager::addError("data", "invalid_data_filter_object");
                $this->prepareExit();
                return ActionState::DENIED;
            }

            // Send the client the fetched attachments.
            $this->response->setResponseCode(200)->send();
            $this->renderer->render(APIData::encode(array("data" => $result)));
            return ActionState::SUCCESS;
        }
        
        /**
         * Executes the process of creating a new attachment.
         */
        public function postAction()
        {
            // Acquire file data.
            $attachmentData = $this->request->getFiles("attachment");
            
            // Check mime type if configured to do so.
            if (Application::getConfig()->attachment->checkMimeType) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                if ($attachmentData["type"] == finfo_file($finfo, $attachmentData["tmp_name"])) {
                    ErrorManager::addError("attachment_error", "invalid_mime_type");
                    $this->prepareExit();
                    return ActionState::DENIED;
                }
            }
            
            // Grab attachment table.
            $attachmentTable = TableCache::getTableFromCache("Attachment");
            
            // Create new attachment entry.
            $attachment = new Attachment();
            $attachment->fileHandle = $attachmentData["name"];
            $attachment->fileType = $attachmentData["type"];
            
            // Insert new attachment entry.
            $attachmentTable->insert($attachment);
            
            // Grab ID of inserted attachment entry.
            $attachment->id = $attachmentTable->lastInsertValue();
            
            // Attempt to create permanent attachment file.
            AttachmentManager::getInstance()->createAttachment($attachment, $attachmentData["tmp_name"]);
            
            // Let client know attachment creation was successful.
            $this->response->setResponseCode(201)->send();
            $this->renderer->render(APIData::encode(array("id" => $attachmentId)));
            return ActionState::SUCCESS;
        }
        
        /**
         * Executes the process of deleting the desired attachment.
         */
        public function deleteAction()
        {
            // Prepare data holder.
            $data = array();
            
            // Ensure data needed is sent to the server.
            $dataProvided = array (
                array ( "key" => "id", "errorType" => "attachment_id", "errorKey" => "missing_attachment_id" )
            );
            if (!$this->fetchData($dataProvided, INPUT_GET, $data)) {
                $this->prepareExit();
                return ActionState::DENIED;
            }
            
            // Get newsletter with provided ID.
            $attachmentTable = TableCache::getTableFromCache("Attachment");
            $attachment = $attachmentTable->getById($data["id"]);
            
            // Error out if no attachment was found to have the ID.
            if (!$attachment) {
                ErrorManager::addError("attachment_id", "invalid_attachment_id");
                $this->prepareExit();
                return ActionState::DENIED;
            }
            
            // Remove attachment file.
            AttachmentManager::getInstance()->removeAttachment($attachment);
            
            // Delete attachment table entry.
            $attachmentTable->deleteByIdentifiers(array ( "id" => $attachment->id ));
            
            // Let client know attachment deletion was successful.
            $this->response->setResponseCode(200)->send();
            return ActionState::SUCCESS;
        }
    }
