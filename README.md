# Alder Wiki

This is the wiki for the Alder services package. The services of Alder are separated into their own standalone packages, providing an inherent modularity to Alder. Each service exists as a RESTful API that authorised clients may access and execute actions via. A unifying admin panel will be provided that allows for configuring, updating and maintaining of your particular instances of the Alder services.

Alder provides a set of services that are intended to satisfy the requirements of various segments of web-based business. The primary focus driving its development is that of satisfying the specific requirements for game development, however due to the modularity of Alder, it can be tailored with ease to various other needs.

The services currently on offer by Alder are:
> * [Public Authentication](https://github.com/RegrowthStudios/Alder/wiki/Public-Authentication)
> * [Internal Authentication](https://github.com/RegrowthStudios/Alder/wiki/Internal-Authentication)
> * [Social](https://github.com/RegrowthStudios/Alder/wiki/Social)
> * [Public](https://github.com/RegrowthStudios/Alder/wiki/Public)
> * [Resources](https://github.com/RegrowthStudios/Alder/wiki/Resources)
> * [Store](https://github.com/RegrowthStudios/Alder/wiki/Store)

By separating out the services, Alder is suitable for providers small or large. Each module is designed to easily be merged with any of the others to run from the same location. Each module builds off the core Alder library that is packages with any module you download. Further, all other modules currently _require_ the alder public authentication module be used. It is desired that in the future this requirement may be dropped.

While not officially supported yet, it is desired in the future to implement a method to easily extend any module, or Alder as a whole with new modules. Currently it is possible to do so, but there are no specific mechanisms and no promise that architectural decisions will be made with this in mind in the short-term (albeit it is desired that such decisions would facilitate an extensions system in the future).