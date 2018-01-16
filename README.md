# In-Memory List Bundle #

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/6ef9eec4-89f3-40d6-a9f6-c0b192576b8c/mini.png)](https://insight.sensiolabs.com/projects/6ef9eec4-89f3-40d6-a9f6-c0b192576b8c)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/0076ba5bfafe4cee87f07b08acbd0099)](https://www.codacy.com/app/mauretto78/in-memory-list-bundle?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=mauretto78/in-memory-list-bundle&amp;utm_campaign=Badge_Grade)
[![license](https://img.shields.io/github/license/mauretto78/in-memory-list-bundle.svg)]()
[![Packagist](https://img.shields.io/packagist/v/mauretto78/in-memory-list-bundle.svg)]()

This is the official Symfony bundle for [In-Memory List package](https://github.com/mauretto78/in-memory-list).

## Install Guide ##

#### Step 1: Include In-Memory List Bundle in your project with composer:

```bash
composer require mauretto78/in-memory-list-bundle
```

#### Step 2: Setup your config.yml to configure your driver and connection parameters

Here is an example:

```yml
# In Memory List
in_memory_list:
    driver: 'redis'
    parameters:
        scheme: 'tcp'
        host: '127.0.0.1'
        port: '6379'
        options:
          profile: '3.2'
```

Please refer to [In-Memory List page](https://github.com/mauretto78/in-memory-list) for more details.

#### Step 3: Setup your AppKernel.php by adding the InMemoryList Bundle

```php
// ..
$bundles[] = new InMemoryList\Bundle\InMemoryListBundle();
```
#### Step 4: Setup yor routing_dev.yml

Add these lines at the bottom of your `routing_dev.yml` file:

```yaml
_inmemorylist:
    resource: '@InMemoryListBundle/Resources/config/routing.yml'
```

## Usage Guide ##

Caching data in your controller:

```php
public function indexAction(Request $request)
{
    $simpleArray = json_encode([
        [
            'userId' => 1,
            'id' => 1,
            'title' => 'sunt aut facere repellat provident occaecati excepturi optio reprehenderit',
            'body' => "quia et suscipit\nsuscipit recusandae consequuntur expedita et cum\nreprehenderit molestiae ut ut quas totam\nnostrum rerum est autem sunt rem eveniet architecto",
        ],
        [
            'userId' => 1,
            'id' => 2,
            'title' => 'qui est esse',
            'body' => "est rerum tempore vitae\nsequi sint nihil reprehenderit dolor beatae ea dolores neque\nfugiat blanditiis voluptate porro vel nihil molestiae ut reiciendis\nqui aperiam non debitis possimus qui neque nisi nulla",
        ],
    ]);

    /** @var Cache $cache */
    $cache = $this->container->get('in_memory_list');
    $cachedList = $cache->getClient()->create(json_decode($simpleArray), ['uuid' => 'simple-list', 'ttl' => 1000]);

    // replace this example code with whatever you need
    return $this->render('default/index.html.twig', [
        'cachedList' => $cachedList,
        'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
    ]);
}
```

Now you can loop data in your twig files:

```twig
{% for item in cachedList %}
    <li>{{ item.userId }}</li>
    <li>{{ item.id }}</li>
    <li>{{ item.title }}</li>
    <li>{{ item.body }}</li>
{% endfor %}
```

## Symfony Profiler ##

You can manage cached lists through the Symfony Profiler: 

![Symfony Profiler](https://github.com/mauretto78/in-memory-list-bundle/blob/master/Resources/views/data_collector/assets/img/profiler.jpg)

## Support ##

If you found an issue or had an idea please refer [to this section](https://github.com/mauretto78/in-memory-list-bundle/issues).

## Authors

* **Mauro Cassani** - [github](https://github.com/mauretto78)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
