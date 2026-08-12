import requests
import zipfile
import io
import os


# =========================
# Configuration
# =========================

TOKEN = "4|cZr8rDJFlJMV6beYAtILMmBj7xldFNl4NeL21BRnfd7b709e"

URL = "http://127.0.0.1:8000/api/data-export/all"

DATA_FOLDER = "data"


# =========================
# Request CSV ZIP
# =========================

headers = {
    "Authorization": f"Bearer {TOKEN}",
    "Accept": "application/zip",
}


print("Downloading dataset...")

response = requests.get(
    URL,
    headers=headers
)

response.raise_for_status()

print("Download successful!")


# =========================
# Create data folder
# =========================

os.makedirs(DATA_FOLDER, exist_ok=True)


# =========================
# Extract ZIP
# =========================

zip_file = zipfile.ZipFile(
    io.BytesIO(response.content)
)

print("\nFiles inside ZIP:")

for file_name in zip_file.namelist():

    print(f"- {file_name}")

    # Extract only CSV files
    if file_name.endswith(".csv"):

        zip_file.extract(
            file_name,
            DATA_FOLDER
        )


# =========================
# Finished
# =========================

print("\nDataset updated successfully!")

print(f"\nCSV files are stored in:")

print(
    os.path.abspath(DATA_FOLDER)
)
