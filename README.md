** Architecture **

Modèle de données NoSQL :

   - Uid
   - Available null,0,1
   - Start Date 
   - End Date  or plage
   - Geo
   - Discriminant : Hashtag
   - Description 


Service : 

    - add a resource 
    - update a resource
        - rewrite 
        - reindex
    - delete a resource 
        - desindexation
    - percolation: 
        * Store research and percolate 
        * When adding or updating : percolate
        * Send a notification

    - RoutingKey :
        must be written on login
        the email is not enough 

    - Notification
        * Store undelivered message.
        * Redeliver and Service reconnection.
        * Client side : message must be still delivered on slepping application.

    - when adding a resource reverseGeocoding to set address.
