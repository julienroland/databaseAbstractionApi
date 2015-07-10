#Storage

###Version 0.1 - not tested natively

Storage is an abstraction that allow you to store data using drivers like filesystem or mysql.

The storage class use the proxy pattern.

From now, Storage is using **Filesystem** which is included in Burger.

###How to use

- Instanciate the **ProxyStorage** : new ProxyStorage($slotName). The constructor required the name of the slot, it's the name of your "table"
- Use the Storage API which is define in **Burger\Storage\Contract\StorageInterface**




