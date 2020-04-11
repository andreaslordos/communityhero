# CommunityHero

## Inspiration

A problem that every community is facing in those difficult times is that people need to leave their houses, even if it is dangerous for them, since they are a vulnerable group. Everybody needs to shop, and for some it's so difficult to order online, that they think it's impossible. Also, since the demand on delivery services increases, so do delivery times, with some stores needing up to 5 days to deliver your order!

## What it does

It allows anyone to order online, by simply sending an SMS message. The customer just needs to send their shopping list to CommunityHero via SMS, and the items will be delivered to them by a volunteer. We can use contactless credit cards through devices like these connected to the mobile phones of delivery people. This will ensure that the delivery will be done without any contact between the delivery person (aka Community Hero) and the customer.
We aim to make ordering easier for people who are not that great with handling technology, allowing them to collect all their groceries and essentials without risking their lives by leaving the house. This has a huge potential target market, which includes the vast majority of people who are 65+ (more than 15% of the population). This population is often the most vulnerable, with death rates exceeding 20% for the higher age groups. This, added to the size of the market for online deliveries (with estimates claiming that more than 25% of orders are made online) makes us believe that if implemented correctly, CommunityHero can not only strengthen the fabric of the community, but also keep our grandparents and parents safe from harm until the pandemic has ended.
Community Heroes can log in on a web interface and see all of the orders available on a map and choose which ones to order.
How we built it
We use an Android phone as the SMS gateway, which receives incoming SMS messages and forwards them to the backend, built using Django and hosted on Heroku. We use NLP to search for the products requested in the products available, and choose the best combination that minimizes price and distance. The searching is done by splitting all strings into N-grams and using the Jaccard Distance.

## Challenges we ran into

It was difficult to find a way to find a viable business model, since it is based on human interest for others, and volunteering, which may be difficult to promote. However, since we are in the middle of a global pandemic, it is a lot easier to get people to dedicate their time than before.

## The Team

    Andreas Lordos, Software Engineer - focusing on webapp
    Christos Falas, Software Engineer - focusing on backend and delivery service
    Deniz Akansoy, Media Officer - focusing on video and graphics

## What's next for Community Hero

We hope to start small, within our local community, hyperfocusing on our target demographic and continuously reiterating upon our offering to make it more suitable for them. After an initial, short-run "alpha-release" we hope to expand to the rest of Cyprus, and eventually start CommunityHero chapters in other countries if possible, to maximize impact.

Future plans:

   - Make the Messenger bot graphical (include pictures of products)
   - Arrange meetings with supermarkets so that we can integrate them into our platform and build a dataset with real products and prices
   - Make the order choose the least price depending on the number of supermarkets
   - Improve search results for orders from SMS by using more advanced NLP algorithms & Machine Learning when we have more data from past queries
   - Suggest items that are frequently bought by a user over something that was never bought

Post-Corona, we still believe CommunityHero can have a significant effect, as the needs of our target market (online delivery & ordering) will still be left unmet by the current competitors in the market. Focusing on this niche market and introducing this extremely large, underserved portion of the population into the online food & delivery service will allow us to expand our business and in turn, our impact, bringing the community closer together, while also incorporating a form of community service through volunteer delivery

## Screenshots
![alt text](https://media.discordapp.net/attachments/590850442049749004/698517825240694894/Screenshot_20200411-160012.jpg?width=386&height=686)
