<?php
    $productJson='{
        "productList": [
            {
            "id": 1,
            "img": "assets/images/product/img-1.png",
            "hoverimg": "assets/images/product/img-01.png",
            "name": "Headphone Boot s4",
            "brand": "Premium Sound with Custom EQ",
            "oldprice": "$24.60",
            "newprice": "22.00",
            "discount": "10%"
            },
            {
            "id": 2,
            "img": "assets/images/product/img-2.png",
            "hoverimg": "assets/images/product/img-02.png",
            "name": "Headphone Boot-Xe",
            "brand": "Premium Sound with Custom EQ",
            "oldprice": "$20.40",
            "newprice": "26.00",
            "discount": "15%"
            },
            {
            "id": 3,
            "img": "assets/images/product/img-3.png",
            "hoverimg": "assets/images/product/img-03.png",
            "name": "Headphone Boot s5",
            "brand": "Premium Sound with Custom EQ",
            "oldprice": "$26.00",
            "newprice": "20.00",
            "discount": "12%"
            },
            {
            "id": 4,
            "img": "assets/images/product/img-4.png",
            "hoverimg": "assets/images/product/img-04.png",
            "name": "Headphone Boot-X",
            "brand": "Premium Sound with Custom EQ",
            "oldprice": "$20.00",
            "newprice": "24.50",
            "discount": "14%"
            },
            {
            "id": 5,
            "img": "assets/images/product/img-5.png",
            "hoverimg": "assets/images/product/img-05.png",
            "name": "Headphone Boot-7s",
            "brand": "Premium Sound with Custom EQ",
            "oldprice": "$28.00",
            "newprice": "34.00",
            "discount": "25%"
            },
            {
            "id": 6,
            "img": "assets/images/product/img-6.png",
            "hoverimg": "assets/images/product/img-06.png",
            "name": "Headphone Boot-v4",
            "brand": "Premium Sound with Custom EQ",
            "oldprice": "$25.60",
            "newprice": "20.00",
            "discount": "16%"
            }
        ]
    }';

    echo json_encode($productJson);
?>