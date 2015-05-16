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
You could also provide some specific params, such as 'searchable' and 'preGet'. Where 'searchable' is array of columns which will be searchable (what a twist!), and 'preGet' is closure that will applied to query before all other filters (search, sort, etc.)
```php
$tableSettings = [
    'columns' => [
        'id' => '#',
        'title' => 'Title',
        'message' => 'Message',
        'sent_at' => 'Sent',
    ],
    'sortable' => [
        'id',
        'sent_at'
    ],
    'searchable' => [
        'title'
    ],
    'preGet' => function($query) {
        /**
        * @var $query \Illuminate\Database\Query\Builder
        */
        return $query->where('type', 'group');
    },
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