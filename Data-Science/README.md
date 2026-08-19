# Planora — Data Science Module

This repository contains the analytics engine I built for Planora as the project's data scientist. It reads the website's data, calculates productivity KPIs per user, and generates interactive charts — all packaged into one JSON response ready for the backend to use.

---

## My Role & What I Built

As the data scientist on this project, I designed and wrote the full analytics pipeline from scratch, split into four single-purpose files:

1. **`data_processing.py`** — This is where I call and clean the data. It reads the raw CSV files and does the cleaning: removing duplicate task entries and converting date columns (like deadlines and creation dates) into proper datetime objects, so the rest of the pipeline can work with them safely.

2. **`kpis.py`** — This file has the function that calculates the KPIs. It takes the cleaned data and a specific user's ID, filters everything down to that user's courses, tasks, and study plans, and returns three calculated metrics as a dictionary.

3. **`visuals.py`** — This file has the function that builds the visuals. It takes the same filtered data and generates two chart configurations, and returns them in JSON-ready form (a Python dictionary that converts directly to JSON).

4. **`connect.py`** — This is the file that connects everything together. It calls the functions from the three files above, combines the KPIs and the visuals into one structure, and returns the final response as a JSON string — formatted to be directly usable by the backend.

I designed the pipeline this way (four separate files instead of one script) so each part has one clear responsibility: get the data, calculate the numbers, build the charts, and package the response. This also made it easier to test and fix each part on its own.

---

## Libraries Used

| Library | Why I used it |
|---|---|
| **pandas** | The core library for this project — reading CSV files, filtering by user, grouping, and aggregating data for both the KPIs and the visuals. |
| **plotly** | Used to build the charts instead of Matplotlib or Seaborn. Matplotlib and Seaborn only produce static images, but Plotly generates interactive chart configurations (JSON-based), which is exactly what a modern web frontend needs to render dynamic, clickable charts. It was also chosen because a Plotly figure converts directly into a JSON object (`fig.to_dict()`) — so the exact same JSON I put in the API response is what the frontend needs to render the chart, with no extra conversion step on either side. |

*(`numpy` appears in `requirements.txt` as a dependency pulled in automatically by pandas/plotly — it is not used directly in my code.)*

---

## Project File Structure

```
Data-Science/
├── README.md
├── requirements.txt
├── data/                     # CSV files go here locally (not committed to git)
│   ├── users.csv
│   ├── courses.csv
│   ├── study_plans.csv
│   └── tasks.csv
└── src/
    ├── data_processing.py     # loads + cleans the CSVs
    ├── kpis.py                 # calculates the KPIs
    ├── visuals.py               # builds the charts
    └── connect.py                # entry point, combines everything into one JSON response
```

---

### What each KPI means
- **Task Completion Rate** — percentage of a user's tasks marked as `completed`.
- **Time Utilization Rate** — completed task hours divided by the user's total available study hours (from `study_plans.csv`).
- **Overall Productivity Score** — a weighted combination: `(completion rate × 0.6) + (time utilization × 0.4)`.

---

## How to Install (Local Setup)

1. Clone the repository:
   ```bash
   git clone https://github.com/IEEE-ZSB-GP-T4/Data-Science.git
   cd Data-Science
   ```

2. Create and activate a virtual environment:
   ```bash
   python3 -m venv .venv
   source .venv/bin/activate      # on Windows: .venv\Scripts\activate
   ```

3. Install dependencies:
   ```bash
   pip install -r requirements.txt
   ```

4. Place the 4 CSV files inside a `data/` folder next to `src/` (or point `DATA_DIR` somewhere else — see below).

---

## How to Use

Run the script directly, passing the target user's ID as an argument:

```bash
python src/connect.py 5
```

This prints a JSON string, shaped like this:

```json
{
    "status": "success",
    "message": "Data retrieved successfully",
    "data": {
        "user_id": 5,
        "dashboard": {
            "kpis": {
                "user_id": 5,
                "task_completion_rate": 72.5,
                "time_utilization_rate": 58.3,
                "overall_productivity_score": 66.8
            },
            "visuals": {
                "tasks_status_donut": { "...": "full Plotly figure object" },
                "tasks_by_priority_bar": { "...": "full Plotly figure object" }
            }
        }
    }
}
```

### Changing where the CSVs are read from

By default, the code looks for the CSVs in a `data/` folder next to `src/`. To use a different folder — for example, on the backend's server — set the `DATA_DIR` environment variable before running the script:

```bash
DATA_DIR=/path/to/exported/csvs python src/connect.py 5
```

---

## Integration with the Backend

My work connects only to the **Backend** repository — the backend is responsible for passing my output to the frontend, so this section covers the backend side only.

- This script runs as a **command**, not a web server. Each run needs one argument: the target `user_id`.
- The backend should set `DATA_DIR` to point at the folder where its scheduled CSV export writes its files, so the script always reads fresh data automatically.
- The 4 required files and their key columns:
  - `users.csv` — `id`
  - `courses.csv` — `id`, `user_id`
  - `tasks.csv` — `id`, `course_id`, `status`, `priority`, `estimated_hours`, `created_at`, `deadline`
  - `study_plans.csv` — `user_id`, `available_hours`, `created_at`
- Suggested way to call it from Laravel:
  ```php
  use Illuminate\Support\Facades\Process;

  public function getDashboard($userId)
  {
      $result = Process::run("python path/to/src/connect.py {$userId}");

      if ($result->successful()) {
          $dashboardData = json_decode($result->output(), true);
          return response()->json($dashboardData);
      }

      return response()->json(['error' => 'Failed to process data'], 500);
  }
  ```
- The script prints **one JSON object** to standard output and nothing else — the backend can forward it to the frontend as-is, with no extra formatting needed on either side.

---

## Branching Strategy

This repository follows the same three-tier model as the rest of the organization:

```
feature/<name> → dev → main
```

- **`main`** — production-ready code only.
- **`dev`** — integration branch; features are merged and tested here first.
- **`feature/<name>`** — one branch per individual feature or task.

Rules:
- Never commit directly to `main` or `dev`.
- Always pull the latest `dev` before creating a feature branch.
- Open a Pull Request into `dev` for every change — never directly into `main`.
- Resolve merge conflicts locally before opening a Pull Request.
