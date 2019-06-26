# Data Interfaces, Service Interfaces and Repository Interfaces

1. The first thing we have to do to create Web API is to create Data interface in Api/Data directory that contains
iteminterface.php composed of magic getters.

2. Then create an Interface repository (ItemRepositoryInterface.php)in API directory.

3. Then create ItemRepository in Model folder.

4. Now we map ItemInterfaces (ItemInterface.php) to Classes in Model and this is done via di.xml using preferences.

5. Preference is kind of di configuration that maps interface to its type(class). For example a preference for ItemInterface and specify type as EquipmentItem in di configuration then whenever ItemIterface
is requested an instance of EquipmentItem is returned.


6. Now create webapi.xml configuration in side etc directory

7. Then use 127.0.0.1/magento23demo/rest/V1/Lmap in browser