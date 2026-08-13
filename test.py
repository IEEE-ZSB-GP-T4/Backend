import requests
import zipfile
import io
import os
import pandas as pd
import json


# =========================
# Configuration
# =========================

TOKEN = "5|s7gRqEf5pP3i92fhyKqyUUDhlMUBZyNZnbI52J2Qeea13bdc"

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

os.makedirs(
    DATA_FOLDER,
    exist_ok=True
)


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
# Read Study Plans
# =========================

study_plans_path = os.path.join(
    DATA_FOLDER,
    "study_plans.csv"
)


if os.path.exists(study_plans_path):

    print("\n==============================")
    print("Reading Study Plans")
    print("==============================\n")

    df = pd.read_csv(
        study_plans_path
    )

    print("Study Plans Data:\n")

    print(
        df.head()
    )


    # =========================
    # Check generated_plan
    # =========================

    if "generated_plan" in df.columns:

        print("\n==============================")
        print("Parsing Generated Plans")
        print("==============================\n")


        parsed_plans = []


        # Loop through study plans
        for _, row in df.iterrows():

            study_plan_id = row["id"]


            try:

                generated_plan = json.loads(
                    row["generated_plan"]
                )


                print(
                    f"\nStudy Plan ID: {study_plan_id}"
                )

                print(
                    json.dumps(
                        generated_plan,
                        indent=4,
                        ensure_ascii=False
                    )
                )


                # =========================
                # Convert generated plan
                # to rows
                # =========================

                if isinstance(generated_plan, list):

                    for item in generated_plan:

                        parsed_plans.append({
                            "study_plan_id": study_plan_id,
                            "user_id": row.get("user_id"),
                            "available_hours": row.get(
                                "available_hours"
                            ),
                            **item
                        })


                elif isinstance(generated_plan, dict):

                    parsed_plans.append({
                        "study_plan_id": study_plan_id,
                        "user_id": row.get("user_id"),
                        "available_hours": row.get(
                            "available_hours"
                        ),
                        **generated_plan
                    })


            except (
                json.JSONDecodeError,
                TypeError
            ):

                print(
                    f"\nError parsing generated_plan "
                    f"for Study Plan ID: {study_plan_id}"
                )


        # =========================
        # Create Parsed DataFrame
        # =========================

        if parsed_plans:

            parsed_df = pd.DataFrame(
                parsed_plans
            )

            print("\n==============================")
            print("Parsed Study Plans DataFrame")
            print("==============================\n")

            print(
                parsed_df
            )


            # =========================
            # Save Parsed CSV
            # =========================

            parsed_file_path = os.path.join(
                DATA_FOLDER,
                "parsed_study_plans.csv"
            )

            parsed_df.to_csv(
                parsed_file_path,
                index=False
            )

            print(
                "\nParsed study plans saved successfully!"
            )

            print(
                f"File: {os.path.abspath(parsed_file_path)}"
            )


        else:

            print(
                "\nNo parsed study plan data found."
            )


    else:

        print(
            "\nColumn 'generated_plan' "
            "was not found in study_plans.csv"
        )


else:

    print(
        "\nstudy_plans.csv was not found!"
    )


# =========================
# Finished
# =========================

print("\n==============================")
print("Dataset updated successfully!")
print("==============================")

print("\nCSV files are stored in:")

print(
    os.path.abspath(DATA_FOLDER)
)
