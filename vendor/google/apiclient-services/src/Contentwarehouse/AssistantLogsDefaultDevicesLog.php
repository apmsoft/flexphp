<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Contentwarehouse;

class AssistantLogsDefaultDevicesLog extends \Google\Collection
{
  protected $collection_key = 'nearbyDefaultDevices';
  protected $localDefaultDevicesType = AssistantLogsDefaultDeviceFlex\Annona\Log::class;
  protected $localDefaultDevicesDataType = '';
  protected $nearbyDefaultDevicesType = AssistantLogsDefaultDeviceFlex\Annona\Log::class;
  protected $nearbyDefaultDevicesDataType = 'array';

  /**
   * @param AssistantLogsDefaultDeviceLog
   */
  public function setLocalDefaultDevices(AssistantLogsDefaultDeviceLog $localDefaultDevices)
  {
    $this->localDefaultDevices = $localDefaultDevices;
  }
  /**
   * @return AssistantLogsDefaultDeviceLog
   */
  public function getLocalDefaultDevices()
  {
    return $this->localDefaultDevices;
  }
  /**
   * @param AssistantLogsDefaultDeviceLog[]
   */
  public function setNearbyDefaultDevices($nearbyDefaultDevices)
  {
    $this->nearbyDefaultDevices = $nearbyDefaultDevices;
  }
  /**
   * @return AssistantLogsDefaultDeviceLog[]
   */
  public function getNearbyDefaultDevices()
  {
    return $this->nearbyDefaultDevices;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AssistantLogsDefaultDevicesFlex\Annona\Log::class, 'Google_Service_Contentwarehouse_AssistantLogsDefaultDevicesLog');