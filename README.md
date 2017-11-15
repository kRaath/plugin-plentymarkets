# plugin-plentymarkets-ekomifeedback

The eKomi product review container allows an easy integration of eKomi Reviews and ratings into your webshop. This allows you to collect verified reviews, display eKomi seal on your website and get your seller ratings on Google and individual positioning of product reviews and includes the Google rich snippet functionality.
<p>
<strong>eKomi Feedback Features:</strong>
</p>
<ul>

<li>Collect order and/or product base Reviews</li>
<li>Supports Simple, Configurable, Grouped and Bundle products</li>
<li>Manage Reviews: our team of Customer Feedback Managers, reviews each and every review for any terms which are not allowed and also put all negative reviews in moderation.</li>
<li>Publish reviews on search engines: Google, Bing, Yahoo!</li>
<li>Easy Integration with eKomi.</li>
<li>Get Google Seller Ratings.</li>
<li>Increase Click through Rate by over 17%</li>
<li>Increase conversion Rate</li>
<li>Product total reviews</li>
<li>Product avg reviews (star rating)</li>
<li>List of reviews with pagination and sorting options</li>
<li>Rating schema for google structured data</li>
<li>Mini star ratings</li>
<li>The parent /child review display</li>

</ul>

<p>eKomi is available in English, French, German, Spanish, Dutch, Italian, Russian and Polish<br />If you have any questions regarding the plugin, please contact your eKomi Account Manager.</p>

<p><b>Please note</b> that you will need an eKomi account to use the plugin. To create an eKomi account, go to 
<a href='http://eKomi.com'>eKomi.com</a>

## Requirements

- plentymarkets version 7.0.0
- [IO Plugin](https://marketplace.plentymarkets.com/plugins/templates/IO_4696)
- [Ceres Plugin](https://marketplace.plentymarkets.com/plugins/templates/Ceres_4697)

## Known issues
- Not any known issue  

## Guides
1. [User Guide](https://ekomi01.atlassian.net/wiki/display/PD/Plentymarkets+-+Official+eKomi+Plugins)

### Installation

Follow these steps to install the plugin.

1. Login to Admin Panel
 
 
2. Go Start » Plugins


3. Add New Plugin
 
 
4. Add through Git
 
 
5. Enter Plugin Git URL & Git Account Credentials

    Remote Url: 
    ```
    https://github.com/ekomi-ltd/plugin-plentymarkets-ekomifeedback.git
    ```
    User name: --your git username

    Password:  --your git password

    After inserting the details click on Test Connection button. It will validate the details.

    Branch: master

    And then Click on Save button.
 
6. Fetch The Latest Plugin Changes

7. Select Clients
    - Click on Search icon
    - Choose Client(s)

8. Deploy EkomiFeedback Plugin In Productive It will take few minutes and then productive icon will turn to blue.
 

9. Plugin Configuration

* Go to EkomiFeedback » Configuration
 
  - Enable / Disable The Plugin
  - Insert your Interface Shop Id
  - Insert your Interface Shop Secret
  - Enable / Disable Product Reviews ( if enabled, product attributes will also be sent to eKomi i.e.  product id, name, image and URL )
  - Enable / Disable Group Reviews ( if enabled, Reviews of child/variants products will also be added  )
  - Select Mode. (for SMS, mobile number format should be according E164)
  - Insert Client Store Plenty IDs. Multiple comma separated Plenty ID can also be added.(optional)
  - Select Order Statuses on which you want to send information to eKomi.
  - Client Store plenty IDs comma separated (optional) to activate to client stores /sub shops
  - Select Referrers Filter (out) to filter out the orders.
  - Insert Text when no reviews found.

  **Note:** Please make sure, The Shop Id and Secret is correct. In the case of invalid credentials the plugin will not work.
 
10. Save the configuration form by clicking on Save Icon


11. Waite for 15 minutes


12. Go Start » Plugins » Content
   - Activate mini stars counter
     >Find **_Mini Stars Counter (EkomiFeedback)_**        
        Select container where to display      
        i.e Tick **_Single Item: Before price_**
  
  
  - Activate Reviews Container Tab
  	>Find **Reviews Container Tab (EkomiFeedback)**<br>
        Select container **_Single Item: Add detail tabs_**
  - Activate Reviews Container
	>Find **Reviews Container (EkomiFeedback)**<br>
        Select container **_Single Item: Add content to detail tabs_**
 

## Built With

* plentymarkets stable 7 framework

## Versioning

### v1.0.0 (16-11-2017)

- A complete working plugin

## Authors

* **eKomi** - [github profile](https://github.com/ekomi-ltd)

See also the list of [contributors](https://github.com/ekomi-ltd/plugin-plentymarkets-ekomifeedback/graphs/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
