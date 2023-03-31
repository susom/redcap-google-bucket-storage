# Google Storage
This EM will allow users to upload files from REDCap form/survey directly into your Google Storage bucket. The upload is 
happening directly from user client to bucket with no REDCap as middleware. 

## Setup Instructions:

1. Create your REDCap project (take note of PID)
2. Create (or log into) your GCP Project that will house data for this REDCap project.
  - You can use one GCP project and have different buckets for different REDCap projects, or you can create a separate Goolge project for each REDCap project.  It really depends on how you want to control access to the uploaded files once they are in Google.
3. Create a Google Storage Service account that will access the bucket on behalf of your REDCap project. https://cloud.google.com/iam/docs/creating-managing-service-accounts
  - You do not need to grant any permissions to the service account at this time
4. After creating service account, click on the hamburger on the right to goto the Manage Keys section and generate a new JSON key file.  This will then download the file to your machine.  When you configure your REDCap EM, you will need the contents of this file the google storage api account service account field.
5. Create your bucket using following link https://cloud.google.com/storage/docs/creating-buckets#storage-create-bucket-console
  - Do not allow public access to the bucket
  - Recommend include the REDCap project id in the bucket name, e.g. `my_redcap_project_name_upload_bucket_pid_12345`
6. Grant the newly created service acccount permission to the bucket by principal with `Storage Object Admin` permissions
    a. Copy the value of client_email key in the service account JSON file. It will be something like '[SERVICE_ACCOUNT_NAME]@[PROJECT_ID].iam.gserviceaccount.com'. 
    b. In Google cloud console go to your newly created bucket. From left main menu click Storage -> Browser -> [YOUR_BUCKET_NAME]
    c. Click on Permission tab then click on add. In New Member box paste the client email you copied from the JSON file. 
    d. in role box search for storage admin. then click Save. 
7. You must update the CORS settings for your bucket in order to be able to push data from REDCap.
 - Use `gutil` to update the settings - to do this, you need to have the [Google Cloud CLI](https://cloud.google.com/storage/docs/gsutil_install) installed.  
 - First, view your bucket's CORS settings.  They should be null:
 ```
 gcloud storage buckets describe gs://hne-collaborator-redcap-uploads-pid28042 --format="default(cors)"
 ```
 - Second, create a `.json` file that will store the cors settings you want to set.  Here is an example file - you will need to update the `origin` array to be the url(s) for your REDCap.  If you use a survey proxy where your external URL is different than your internal URL, you can list multiple values in the array.  Save the file, e.g. something like `my_bucket_cors.json`:
 ```
 [ { "origin": [ "https://redcap.stanford.edu" ], "responseHeader": [ "Content-Type", "X-Requested-With", "Access-Control-Allow-Origin", "x-goog-resumable" ], "method": [ "GET", "HEAD", "DELETE", "POST", "PUT", "OPTIONS" ], "maxAgeSeconds": 3600 } ]
 ```
 - Third, from your console, do the following:
 ```
 -- Ensure you are in your google project
 gcloud config set project YOUR-GCP-PROJECT-NAME
 -- View your current cors settings (should be null)
 gcloud storage buckets describe gs://YOUR-BUCKET-NAME --format="default(cors)"
 -- Now, update the CORS settings
 gsutil cors set my_bucket_cors.json gs://YOUR-BUCKET-NAME
 -- Now, verify the cors settings took:
 gcloud storage buckets describe gs://YOUR-BUCKET-NAME --format="default(cors)"
 ```
 8. Configure the REDCap EM by following the standard EM configuration instructions
 
 
#### REDCap EM configuration:
1. Click on External Modules in left Main Menu. then click configure for `GoogleStorage - v9.9.9`
2. from your Service Account JSON file copy the value of project_id and paste in `Google Storage Project ID`
3. Copy the json file content and save it into `Google Storage API Service Account`
4. Define the list of buckets name that you want to access via this EM.

![Alt text](assets/images/redcap-em-config.png?raw=true "REDCap EM Config" )

#### REDCap Form Configuration:
1. Create new text form field. 
2. In Action Tags/Field Annotation box add following `@GOOGLE-STORAGE=[YOUR_BUCKET_NAME]`

![Alt text](assets/images/redcap-field-config.png?raw=true "REDCap Field Config")
