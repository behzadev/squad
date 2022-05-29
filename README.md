# Task
In this task I assumed that a hook has been setup on our SMS service provider side and an endpoint of ours will get hit by SMS service provider with new messages from users posting `number` and `message` to us.

After getting a new message, we'll put the message in a queue and return response to the user. from that point on, we need to `queue:work` and handle the messages in queue to see if we should consider this message as a winner or not.

Two other endpoints implemented as well for storing new codes and checking if a cell number with specific code is among winners or not.

### Tests
```
php artisan test
```
