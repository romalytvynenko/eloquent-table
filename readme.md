# Install
Add folowing line to your `composer.json`:
```json
"romalytvynenko/eloquent-table": "dev-master"
```

Add following lines to `config/app.php` (providers)
```
'Romalytvynenko\EloquentTable\EloquentTableServiceProvider'
```

And this part to `aliases` section
```
'EloquentTable' => 'Romalytvynenko\EloquentTable\Engine\EloquentTable'
```

# Usage
First of all, cerate EloquentTable object.
```php
$exampleTable = new EloquentTable('Example', $tableSettings);
```
Where first parameter is model name, and $tableSettings is special table settings used for current table.
```php
$tableSettings = [
    'columns' => [
        'id' => '#',
        'message' => 'Message',
        'sent_at' => 'Sent',
    ],
    'sortable' => [
        'id',
        'sent_at'
    ]
];
```
And now you could render table using `show` method:
```php
$exampleTable->show();
```

### Changing column output
For changing columns output you can use closures for hooking output:

```php
$table->columnOutput('id', function($item){
    /**
     * @var $item \Eloquent
     */
    return 'Item - ' . $item->id;
});
```
Where `id` is example of column name, and anonymous function - is the example of a function wich result will be used *instead* of original column value.

### Your own table layout
You can easyli create your own table layout. For those purpose look at views/table.php sources, and feel free to create iyour own. You can use it passing to the `show` function your view name:
```php
$exampleTable->show('admin.table');
```