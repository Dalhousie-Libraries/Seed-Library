/**
 *  Item controller
 */
app.controller("item", function($scope, ItemService, $filter, ngTableParams)
{
    // Retrieves a specific item
    $scope.getItem = function(id)
    {
        var item = ItemService.getItem(id);

        // Sets item model
        item.success(function(data) {
            $scope.item = data;
        });
    };

    // Retrieves all items
    $scope.getItems = function()
    {
        var items = ItemService.getItems();

        items.success(function(data) {
            // Sets table up
            $scope.itemsTable = new ngTableParams({
                page: 1, // show first page
                count: 10, // count per page
            }, {
                total: data.length, // length of data
                getData: function($defer, params) {
                    // use build-in angular filter
                    var orderedData = params.sorting() ?
                            $filter('orderBy')(data, params.orderBy()) : data;

                    //$defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));

                    // use build-in angular filter
                    var filteredData = params.filter() ?
                            $filter('filter')(orderedData, params.filter()) : orderedData;

                    $scope.items = filteredData.slice((params.page() - 1) * params.count(), params.page() * params.count());

                    params.total(filteredData.length); // set total for recalc pagination
                    $defer.resolve($scope.items);
                }
            });
        });
    }
});

/**
 *  Packet controller
 */
app.controller("packet", function($scope, PacketService, CartService, $filter, ngTableParams)
{
    $scope.getPackets = function(itemId)
    {
        var packets = PacketService.getPackets(itemId);

        packets.success(function(data) {
            $scope.packets = CartService.addFilter(data);

            // Sets table up
            $scope.packetsTable = new ngTableParams({
                page: 1, // show first page
                count: 10, // count per page
            }, {
                total: data.length, // length of data
                getData: function($defer, params) {
                    // use build-in angular filter
                    var orderedData = params.sorting() ?
                            $filter('orderBy')(data, params.orderBy()) : data;

                    $defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
                }
            });
        });
    }
    
    /**
     * Gets all packets related to a user.
     * @param int userId
     * @param bool edit Indicates that it is being accessed from editing page
     */
    $scope.getUserPackets = function(userId, edit)
    {
        var packets = PacketService.getUserPackets(userId, edit);

        packets.success(function(data) {
            // Sets table up
            $scope.packetsTable = new ngTableParams({
                page: 1, // show first page
                count: 10, // count per page,
                sorting: {   // initial sorting
                    family: 'asc',
                    id: 'asc'
                }
            }, {
                total: data.length, // length of data
                getData: function($defer, params) {
                    // use build-in angular filter
                    var orderedData = params.sorting() ?
                            $filter('orderBy')(data, params.orderBy()) : data;

                    $defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
                    
                    // use build-in angular filter
                    var filteredData = params.filter() ?
                            $filter('filter')(orderedData, params.filter()) : orderedData;

                    $scope.packets = filteredData.slice((params.page() - 1) * params.count(), params.page() * params.count());

                    params.total(filteredData.length); // set total for recalc pagination
                    $defer.resolve($scope.packets);
                }
            });
        });
    }
    
    $scope.history = new Array();
    $scope.getPacketHistory = function(packetId) 
    {
        var history = PacketService.getPacketHistory(packetId);
        
        history.success(function(data) {
            var packet;

            if (data) 
            {
                packet = data;
                do {                    
                    $scope.history.push(packet);
                    packet = packet.accession.parent;
                } while(packet);
            }
        });
    }

    $scope.getPacketsCart = function()
    {
        $scope.packets = CartService.getPackets();
    }

    // Adds a packet to cart (or remove it)
    $scope.addToCart = function(packet)
    {
        // If not in the cart, adds it to the cart, removes it otherwise
        if (packet.inCart)
            CartService.remove(packet);
        else
            CartService.add(packet);

        packet.inCart = !packet.inCart;
    }

    // Removes a packet from cart
    $scope.removeFromCart = function(packet)
    {
        CartService.remove(packet);
    }
});

/**
 *  Cart controller
 */
app.controller("cart", function($scope, CartService)
{
    $scope.countPackets = function()
    {
        var packets = CartService.getPackets();

        return packets ? packets.length : 0;
    };

    $scope.clearCart = function()
    {
        CartService.clear();
    }

    $scope.doCheckout = function(id)
    {
        // Do checkout process
        var checkout = CartService.doCheckout(id);

        // Loads post checkout data
        checkout.success(function(data) {
            var newContent = "";
            
            // Saves response
            reservedPackets = data;
            
            // Replaces page content
            $("#packets_container_pre").fadeOut("fast", function() {
                // Clears page
                $(this).html('');
                
                // Insert data in DOM format
                var info = "";
                $.each(data, function(key, value) {
                    switch(value.response) {
                        case 'NULL':
                            info = "Requested packet does not exist";
                            break;
                        case 'RESERVED':
                            info = "Requested packet is already reserved.";
                            break;
                        case 'FAILURE':
                            info = "An error ocurred while reserving this packet.";
                            break;
                        default:
                            info = "Reserved";
                            break;
                    }
                    
                    // Assemble data into a table row
                    newContent += 
                    "<tr>" +
                        "<td>" + value.seed + "</td>" +
                        "<td class='text-center'>" + value.amount + "</td>" +
                        "<td class='" + (value.ok ? 'success' : 'danger')  + "'>" + 
                            (value.ok ? "<span class='glyphicon glyphicon-ok'></span> " : "<span class='glyphicon glyphicon-remove'></span> ")   + info +
                        "</td>" +
                    "</tr>";
                })
                
                // Append new content to page
                $('#packets_post').children('tbody').append(newContent);
                
                // Shows post checkout page
                $('#packets_container_post').fadeIn("slow");
            });
            
            // Clears cart before finishing
            CartService.clear();
        });
    };
});

// User controller
app.controller("user", function($scope, UserService, $filter, ngTableParams, $compile) 
{
    $scope.getRequests = function(action, userId, admin) 
    {
        var requests = UserService.getRequests(action, userId, admin);

        requests.success(function(data) {
            // Converts datetime to a readable format
            $.each(data, function(key, value) {
                if(value.requested_at)
                    value.requested_at = dateToISOString(value.requested_at, '-0300');
                
                if(value.reserved_until)
                    value.reserved_until = dateToISOString(value.reserved_until, '-0300');
                
                if(value.checked_out_date)
                    value.checked_out_date = dateToISOString(value.checked_out_date, '-0300');
                
                if(value.checked_in_date)
                    value.checked_in_date = dateToISOString(value.checked_in_date, '-0300');
            });
            
            $scope.requests = data;

            // Sets table up
            $scope.requestsTable = new ngTableParams({
                page: 1, // show first page
                count: 10, // count per page
            }, {
                total: data.length, // length of data
                getData: function($defer, params) {
                    // use build-in angular filter
                    var orderedData = params.sorting() ?
                            $filter('orderBy')(data, params.orderBy()) : data;

                    $defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
                }
            });
        });
    };
});