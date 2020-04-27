import os
import requests
import csv
from random import randint


id_to_supermarket = {1: "sklavenitis", 2: "lidl", 3: "spar", 4: "aldi", 5: "carrefour", 6: "pharmacy 1", 7: "pharmacy 2", 8: "pharmacy 3"}
responses=[]

for x in range(1,9):
	shop_id=str(x)
	url=f'http://rhubarb-cake-22341.herokuapp.com/api/download_products/{shop_id}'
	response = requests.get(url)
	responses.append(response)


for s in range(1,len(responses)+1):
        r = responses[s-1]
        with open(f'{id_to_supermarket[s]}.csv', 'w', newline='') as file:
                writer = csv.writer(file)
                writer.writerow(['id','type','sku','name','status','featured','catalog_visibility','short_description','description','date_on_sale_from','date_on_sale_to','tax_status','tax_class','stock_status','backorders','sold_individually','weight','height','reviews_allowed','purchase_note','price','regular_price','manage_stock/stock_quantitiy','category_ids','tag_ids','shipping_class_id','attributes','attributes','default_attributes','attributes','image_id/gallery_image_ids','attributes','downloads','downloads','download_limit','download_expiry','parent_id','upsell_ids','cross_sell_ids', 'position', 'vendor id']) #initial row
                raw = r.content
                formatted = raw.decode()
                lines = formatted.split("\n")
                for x in range(2, len(lines)):
                        final = []
                        line = lines[x]
                        attributes = line.split(",")
                        for attr in attributes:
                                final.append(attr.strip()[1:-1])
                        writer.writerow([final[0],'simple', '', final[1], 1, 0, 'visible', '', '', '', '', 'taxable', '', 1, 0, 0, '', '', 0, '', '', final[2], 1000, '', '', '', '', '', '', '', "/wordpress/wp-content/uploads/2016/09/3aparpoilt.jpg", '', '', '','','','','','', 0, 8]) #product            
