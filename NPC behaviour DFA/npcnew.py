import tkinter as tk
from tkinter import scrolledtext

class NPC:
    def __init__(self):
        self.initial_state = "Idle_Far"
        self.state = self.initial_state
        self.alive = True

        self.transitions = {
            ("Idle_Far", "see_player"): "Chase_Far",
            ("Idle_Far", "player_near"): "Idle_Near",
            ("Idle_Far", "kill"): "Dead",

            ("Idle_Near", "lose_player"): "Idle_Far",
            ("Idle_Near", "see_player"): "Chase_Near",
            ("Idle_Near", "kill"): "Dead",

            ("Wander_Far", "see_player"): "Chase_Far",
            ("Wander_Far", "player_near"): "Attack_Near",
            ("Wander_Far", "kill"): "Dead",

            ("Chase_Far", "player_near"): "Attack_Near",
            ("Chase_Far", "lose_player"): "Wander_Far",
            ("Chase_Far", "low_health"): "Flee_Far",
            ("Chase_Far", "kill"): "Dead",

            ("Chase_Near", "player_near"): "Attack_Near",
            ("Chase_Near", "lose_player"): "Wander_Far",
            ("Chase_Near", "low_health"): "Flee_Far",
            ("Chase_Near", "kill"): "Dead",

            ("Attack_Near", "lose_player"): "Wander_Far",
            ("Attack_Near", "low_health"): "Flee_Far",
            ("Attack_Near", "kill"): "Dead",

            ("Flee_Far", "safe_distance"): "Idle_Far",
            ("Flee_Far", "player_near"): "Flee_Far",
            ("Flee_Far", "kill"): "Dead",
        }

    def reset(self):
        self.state = self.initial_state
        self.alive = True

    def transition(self, input_symbol):
        prev = self.state

        if self.state == "Dead":
            return prev, self.state

        key = (self.state, input_symbol)

        if key in self.transitions:
            self.state = self.transitions[key]
        else:
            pass  # removed print for GUI cleanliness

        if self.state == "Dead":
            self.alive = False

        return prev, self.state

    def describe(self, prev, curr, input_symbol):
        return f"{prev} --[{input_symbol}]--> {curr}"


# ---------- GUI ----------
class NPC_GUI:
    def __init__(self, root):
        self.npc = NPC()
        self.step = 1

        self.root = root
        self.root.title("NPC Behavior Automaton (DFA)")

        # Current state label
        self.state_label = tk.Label(root, text=f"Current State: {self.npc.state}", font=("Arial", 14))
        self.state_label.pack(pady=10)

        # Log area
        self.log = scrolledtext.ScrolledText(root, width=50, height=15, state='disabled')
        self.log.pack(padx=10, pady=10)

        # Buttons
        btn_frame = tk.Frame(root)
        btn_frame.pack()

        buttons = [
            ("See Player", "see_player"),
            ("Approach", "player_near"),
            ("Get Away", "lose_player"),
            ("Attack (Flee)", "low_health"),
            ("Safe Distance", "safe_distance"),
            ("Kill NPC", "kill"),
        ]

        for text, action in buttons:
            tk.Button(btn_frame, text=text, width=18,
                      command=lambda a=action: self.handle_input(a)).pack(pady=2)

        # Reset & Exit
        tk.Button(root, text="Reset", width=18, command=self.reset).pack(pady=5)
        tk.Button(root, text="Exit", width=18, command=root.quit).pack(pady=5)

    def log_message(self, message):
        self.log.config(state='normal')
        self.log.insert(tk.END, message + "\n")
        self.log.config(state='disabled')
        self.log.yview(tk.END)

    def handle_input(self, inp):
        if not self.npc.alive:
            self.log_message("NPC is DEAD. End of automaton.")
            return

        self.log_message(f"\nSTEP {self.step}")
        prev, curr = self.npc.transition(inp)
        self.log_message(self.npc.describe(prev, curr, inp))

        self.state_label.config(text=f"Current State: {self.npc.state}")

        if not self.npc.alive:
            self.log_message("NPC is DEAD. End of automaton.")

        self.step += 1

    def reset(self):
        self.npc.reset()
        self.step = 1
        self.state_label.config(text=f"Current State: {self.npc.state}")
        self.log_message("\nNPC Reset")


# Run GUI
root = tk.Tk()
app = NPC_GUI(root)
root.mainloop()