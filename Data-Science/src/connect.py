import json
import sys
from data_processing import get_processed_data
from kpis import calculate_user_kpis
from visuals import generate_user_visuals

def generate_dashboard_response(user_id):
    # getting the data
    full_data = get_processed_data()

    #getting KPIs
    kpis_data = calculate_user_kpis(full_data, target_user_id=user_id)

    #getting the visuals
    visuals_data = generate_user_visuals(full_data, target_user_id=user_id)

    # the final response
    final_response = {
        "status": "success",
        "message": "Data retrieved successfully",
        "data": {
            "user_id": user_id,
            "dashboard": {
                "kpis": kpis_data,
                "visuals": visuals_data
            }
        }
    }

    # making the response into a json form
    return json.dumps(final_response, ensure_ascii=False, indent=4, default=str)


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"status": "error", "message": "user_id is required"}))
        sys.exit(1)

    try:
        user_id = int(sys.argv[1])
    except ValueError:
        print(json.dumps({"status": "error", "message": "user_id must be a number"}))
        sys.exit(1)

    result = generate_dashboard_response(user_id)
    print(result)


    # 
