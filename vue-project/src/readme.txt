I have a form.

I have two endpoints.
One POST which persists form values. But it does not persist when validation fails.
After a successful POST I have GET which returns saved form values.

This means when something is invalid in form and user continues to update other fields, they are not saved.