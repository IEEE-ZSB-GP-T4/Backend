import requests
import zipfile
import io
import os
import sys


# =========================
# Configuration
# =========================

TOKEN = "5|s7gRqEf5pP3i92fhyKqyUUDhlMUBZyNZnbI52J2Qeea13bdc"

URL = "http://127.0.0.1:8000/api/data-export/all"

DATA_FOLDER = "data"


# =========================
# Request Dataset ZIP
# =========================

headers = {
    "Authorization": f"Bearer {TOKEN}",
    "Accept": "application/zip",
}


print("Downloading dataset...")


try:
    response = requests.get(
        URL,
        headers=headers,
        timeout=60
    )

    # Check HTTP status
    response.raise_for_status()

except requests.exceptions.RequestException as error:

    print("\nDownload failed!")
    print(error)

    sys.exit(1)


print("Download successful!")


# =========================
# Check Response Type
# =========================

content_type = response.headers.get("Content-Type", "")
content_length = response.headers.get("Content-Length", "Unknown")

print(f"\nContent-Type: {content_type}")
print(f"Content-Length: {content_length}")


# Check if response is a valid ZIP
if not zipfile.is_zipfile(io.BytesIO(response.content)):

    print("\nERROR: The server response is not a valid ZIP file.")

    print("\nServer response:")

    try:
        print(response.json())
    except ValueError:
        print(response.text[:2000])

    sys.exit(1)


# =========================
# Create Data Folder
# =========================

os.makedirs(
    DATA_FOLDER,
    exist_ok=True
)


# =========================
# Open ZIP
# =========================

try:

    zip_file = zipfile.ZipFile(
        io.BytesIO(response.content)
    )

except zipfile.BadZipFile:

    print("\nERROR: Downloaded file is corrupted.")

    sys.exit(1)


# =========================
# Show ZIP Files
# =========================

print("\nFiles inside ZIP:")

csv_files = []

for file_name in zip_file.namelist():

    print(f"- {file_name}")

    # Extract only CSV files
    if file_name.endswith(".csv"):

        csv_files.append(file_name)

        zip_file.extract(
            file_name,
            DATA_FOLDER
        )


# =========================
# Check CSV Files
# =========================

if not csv_files:

    print("\nWARNING: No CSV files found inside the ZIP.")

    sys.exit(1)


# =========================
# Finished
# =========================

print("\nDataset updated successfully!")

print("\nDownloaded CSV files:")

for file_name in csv_files:

    print(f"- {file_name}")


print("\nCSV files are stored in:")

print(
    os.path.abspath(DATA_FOLDER)
)
