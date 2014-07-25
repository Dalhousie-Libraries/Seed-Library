/**
 * Factories
 */
app.factory("ItemService", function($http) {
    return {
        "getItem": function($id) {
            return $http.get("../../item/find/" + $id);
        },
        "getItems": function() {
            return $http.get("../item/list");
        }
    };
});

app.factory("PacketService", function($http) {

    return {
        "getPackets": function($itemId) {
            return $http.get("../../packet/item_list/" + $itemId);
        },
        
        "getUserPackets": function($itemId, edit) {
            var upperDir = edit ? "../" : "";
            return $http.get(upperDir + "../packet/requests/lendings/" + $itemId);
        },
        
        "getPacketHistory": function($packetId) {
            return $http.get("../../../packet/history/" + $packetId);
        },
    };
});

app.factory("CartService", function($http) {

    var packetsInCart = $.cookie('packets');

    var packets = JSON.parse(packetsInCart || '[]');

    return {
        "add": function(packet) {
            packets.push({
                "id": packet.id,
                "seed": packet.family + ' (' + packet.species + ') - ' + packet.variety,
                "amount": packet.amount,
                "date_harvest": packet.date_harvest,
                "grow_location": packet.grow_location,
                "germination_ratio": packet.germination_ratio
            });

            this.store();
        },
        "addFilter": function(data) {
            // For each of the packets, verify if it is already in cart
            $.each(data, function(index, element) {
                if (contains(packets, 'id', element.id))
                    element.inCart = true;
                else
                    element.inCart = false;
            });

            return data;
        },
        "getPackets": function() {
            return packets;
        },
        "remove": function(packet) {
            findAndRemove(packets, 'id', packet.id);

            this.store();
        },
        "store": function() {
            $.cookie('packets', JSON.stringify(packets), {expires: 7, path: '/'});
        },
        "clear": function() {
            packets.length = 0;
            this.store();
        },
        "doCheckout": function(id) {
            return $http.post('../item/checkout',
                    {
                        packets: packets,
                        borrowerId: id
                    }
            );
        }
    };
});

app.factory("UserService", function($http) {

    return {
        "getRequests": function(action, userId, admin) {
            var upperDir = admin ? "../../" : "";
            return $http.get(upperDir + "../packet/requests/" + action + "/" + userId);
        },
    };
});