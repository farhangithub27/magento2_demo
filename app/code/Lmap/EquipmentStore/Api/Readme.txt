The first thing we have to do to create Web API is to create Data interface that holds item interface commposed of magic getters and Interface repository in API.


Then create ItemRepository in Model folder.

Now we map ItemInterfaces to Classes in Model and this is done di.xml using preferences.

Preference is kind of di configuration that maps interface to its type(class). For example a preference for ItemInterface and specify type as EquipmentItem in di configuration then whenever ItemIterface
is requested an instance of EquipmentItem is returned.


Now create webapi.xml configuration in side etc directory

Then use 127.0.0.1/magento23demo/rest/V1/Lmap in browser