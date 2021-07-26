# HIDESUBMIT Action Tag

This REDCap External Module allows users to conditionally hide the Submit/Next Page button using an action tag @HIDESUBMIT. If the action tag is on any field that is visible on the current page due to branching logic, the Submit/Next Page button is hidden.

The intention is that this action tag is used on descriptive text fields in which the respondent is informed that they cannot continue due to some reason. For example, a user could create a descriptive text field that shows a warning message such as "You appear to have missed some questions!", and use branching logic to display this field if any of the required fields on the page are empty. As a result of the @HIDESUBMIT action tag, the presence of this descriptive text field will prevent the submit button being displayed.

![Submit button hidden](img/hidesubmit_readme_1.png)

Once all descriptive text fields with @HIDESUBMIT are no longer visible (according to their branching logic), the submit button immediately becomes visible again.

![Submit button visible](img/hidesubmit_readme_2.png)

## Setup & Configuration

Install the module from REDCap module repository and enable over Control Center.

## Usage and Examples

The action tag can be added to any field, although descriptive text fields make the most sense. Hiding the submit button based on the visibility of a question field would be odd, since the response would not be able to be committed to the dataset.

### Ensure all required fields are answered

To prevent a respondent prematurely submitting before all required fields are filled, create a descriptive text field with @HIDESUBMIT, and branching logic as desired, which might be a series of checks as below:

```
[name] <> "" and [email] <> "" and ...
```

For a better respondent experience, this descriptive text field should inform them why they cannot proceed, in the example above, the field displays "You seem to have missed some required questions above!".

### Enforcing survey quota

Aggregate functions like `[aggregate-count:field]` (from v11.0.0) can be used to limit the survey to a certain number of respondents, hiding the submit button if the survey has fulfilled the quota and instead displaying a user-configurable warning such as "This survey is now closed".

### Minimum age

A common use case is to limit a survey to respondents over a certain age. Traditionally, this is done using the survey queue. With @HIDESUBMIT this can be achieved using a descriptive text field and branching logic as follows:

```
datediff([dob],"today","y",true) < 18
```
And thus prevents the creation of empty records.

Note that the logic is reversed between @HIDESUBMIT and the survey queue approach; the survey queue will have the logic for respondents to continue (i.e. `datediff([dob],"today","y",true) >= 18`), whereas the @HIDESUBMIT field contains the logic used to prevent a respondent from continuing.

### Multiple exclusion factors

Multiple @HIDESUBMIT fields are also possible. The submit button will be hidden if there are _any_ fields visible with the action tag. This allows users to construct eligibility surveys with multiple exclusion criteria.

## TODO

- Add action tag instructions to online designer dialogue

## Acknowledgements

This is my first attempt at a REDCap External Module, and I borrowed heavily from other developers including Andy Martin, Ekin Tertemiz, Günther Rezniczek, as well as an earlier hook by Bob Gorczyca. I also drew on help with JavaScript from Luke Stevens, who also suggested the general design of this module.

## Changelog

| Version | Description |
| ------- | -------------------- |
| v1.0.0  | Initial release. |
